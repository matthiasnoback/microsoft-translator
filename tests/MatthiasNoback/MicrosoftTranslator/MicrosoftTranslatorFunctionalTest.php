<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator;

use Buzz\Browser;
use Buzz\Client\Curl;
use MatthiasNoback\MicrosoftOAuth\AccessTokenProvider;
use MatthiasNoback\MicrosoftTranslator\ApiCall\Response\TranslationMatch;
use MatthiasNoback\MicrosoftTranslator\MicrosoftTranslator;
use MatthiasNoback\MicrosoftOAuth\AccessTokenCache;
use Doctrine\Common\Cache\ArrayCache;
use MatthiasNoback\Buzz\Client\CachedClient;

class MicrosoftTranslatorFunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MicrosoftTranslator
     */
    private $translator;

    /**
     * @var Browser
     */
    private $browser;

    protected function setUp()
    {
        $client = new Curl();
        $client->setTimeout(30);
        $this->browser = new Browser($client);

        $clientId = $this->getEnvironmentVariable('MICROSOFT_OAUTH_CLIENT_ID');
        $clientSecret = $this->getEnvironmentVariable('MICROSOFT_OAUTH_CLIENT_SECRET');

        $cache = new ArrayCache();
        $accessTokenCache = new AccessTokenCache($cache);
        $accessTokenProvider = new AccessTokenProvider($this->browser, $clientId, $clientSecret);
        $accessTokenProvider->setCache($accessTokenCache);

        $this->translator = new MicrosoftTranslator($this->browser, $accessTokenProvider);
    }

    public function testTranslate()
    {
        $translated = $this->translator->translate('This is a test', 'nl', 'en');

        $this->assertSame('Dit is een test', $translated);
    }

    public function testTranslateArray()
    {
        $translatedTexts = $this->translator->translateArray(array(
            'This is a test',
            'My name is Matthias',
            'You are naïve!',
        ), 'nl', 'en');

        $this->assertSame(array(
            'Dit is een test',
            'Mijn naam is Matthias',
            'U bent naïef!',
        ), $translatedTexts);
    }

    public function testGetTranslations()
    {
        $translated = $this->translator->getTranslations('This is a test', 'nl', 'en', 1);

        $this->assertEquals(array(new TranslationMatch('Dit is een test', 100)), $translated);
    }

    public function testDetect()
    {
        $text = 'This is a test';

        $detectedLanguage = $this->translator->detect($text);

        $this->assertSame('en', $detectedLanguage);
    }

    public function testDetectArray()
    {
        $texts = array(
            'This is a test',
            'Dit is een test',
        );

        $detectedLanguages = $this->translator->detectArray($texts);

        $this->assertSame(array('en', 'nl'), $detectedLanguages);
    }

    public function testBreakSentences()
    {
        $text = 'This is the first sentence. This is the second sentence. This is the last sentence.';

        $sentences = $this->translator->breakSentences($text, 'en');

        $this->assertSame(array(
            'This is the first sentence. ',
            'This is the second sentence. ',
            'This is the last sentence.',
        ), $sentences);
    }

    public function testSpeak()
    {
        $saveAudioTo = $this->getEnvironmentVariable('MICROSOFT_TRANSLATOR_SAVE_AUDIO_TO');
        if (!is_writable($saveAudioTo)) {
            $this->markTestSkipped(sprintf('Can not save audio file to "%s"', $saveAudioTo));
        }

        $text = 'My name is Matthias';

        $spoken = $this->translator->speak($text, 'en', 'audio/mp3', 'MaxQuality');

        file_put_contents($saveAudioTo.'/speak.mp3', $spoken);
    }

    public function testGetLanguagesForSpeak()
    {
        $languageCodes = $this->translator->getLanguagesForSpeak();
        $this->assertInternalType('array', $languageCodes);
        $this->assertTrue(count($languageCodes) > 30);
    }

    public function testGetLanguagesForTranslate()
    {
        $languageCodes = $this->translator->getLanguagesForTranslate();
        $this->assertInternalType('array', $languageCodes);
        $this->assertTrue(count($languageCodes) > 30);

        return $languageCodes;
    }

    public function testGetLanguageNames()
    {
        $languageCodes = $this->translator->getLanguagesForSpeak();

        $languageNames = $this->translator->getLanguageNames($languageCodes, 'nl');

        foreach ($languageCodes as $languageCode) {
            $this->assertArrayHasKey($languageCode, $languageNames);
        }
    }

    public function testCachedBrowserClient()
    {
        $currentClient = $this->browser->getClient();

        $arrayCache = new ArrayCache();
        $cachedClient = new CachedClient($currentClient, $arrayCache);

        $this->browser->setClient($cachedClient);

        for ($i = 1; $i <= 3; $i++) {
            $this->translator->translate('This is a test', 'nl');
        }

        $this->assertLessThanOrEqual(2, $cachedClient->getMisses()); // at most one for the access token, one for the translation
        $this->assertSame(2, $cachedClient->getHits());

        $this->browser->setClient($currentClient);
    }

    private function getEnvironmentVariable($name)
    {
        if (getenv($name) === false) {
            $this->markTestSkipped(sprintf('Environment variable "%s" is missing', $name));
        }

        return getenv($name);
    }
}
