<?php

namespace MatthiasNoback\Tests\MicrosoftTranslator;

use PHPUnit\Framework\TestCase;

use Buzz\Browser;
use Buzz\Client\Curl;
use Cache\Adapter\PHPArray\ArrayCachePool;
use MatthiasNoback\MicrosoftOAuth\AccessTokenCache;
use MatthiasNoback\MicrosoftOAuth\AzureTokenProvider;
use MatthiasNoback\MicrosoftTranslator\ApiCall\Translate;
use MatthiasNoback\MicrosoftTranslator\MicrosoftTranslator;
use Nyholm\Psr7\Factory\Psr17Factory;

class MicrosoftAzureTranslatorFunctionalTest extends TestCase
{
    /**
     * @var MicrosoftTranslator
     */
    private $translator;

    /**
     * @var Browser
     */
    private $browser;

    protected function setUp(): void
    {
        $client = new Curl(new Psr17Factory());
        $this->browser = new Browser($client, new Psr17Factory());

        $azureKey = $this->getEnvironmentVariable('MICROSOFT_AZURE_KEY');
        $cache = new ArrayCachePool();
        $accessTokenCache = new AccessTokenCache($cache);
        $accessTokenProvider = new AzureTokenProvider($this->browser, $azureKey);
        $accessTokenProvider->setCache($accessTokenCache);

        $this->translator = new MicrosoftTranslator($this->browser, $accessTokenProvider);
    }

    public function testTranslate()
    {
        $translated = $this->translator->translate('This is a test', 'nl', 'en');

        $this->assertSame('Dit is een test', $translated);
        $translated = $this->translator->translate(
            '<p class="name">This is a test</p>',
            'nl',
            'en',
            null,
            Translate::CONTENT_TYPE_HTML
        );

        $this->assertSame('<p class="name">Dit is een test</p>', $translated);

        $translated = $this->translator->translate(
            '<p>This is a test.<span class="notranslate">This is a test.</span></p>',
            'nl',
            'en',
            null,
            Translate::CONTENT_TYPE_HTML
        );

        $this->assertSame('<p>Dit is een test.<span class="notranslate">This is a test.</span></p>', $translated);
    }

    public function testTranslateArray()
    {
        $translatedTexts = $this->translator->translateArray(array(
            'This is a test',
            'My name is Matthias.',
            'You are naïve!',
        ), 'nl', 'en');

        $this->assertSame(array(
            'Dit is een test',
            'Mijn naam is Matthias.',
            'Je bent naïef!',
        ), $translatedTexts);
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
            'Esto es una prueba',
        );

        $detectedLanguages = $this->translator->detectArray($texts);

        $this->assertSame(array('en', 'es'), $detectedLanguages);
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

    public function testGetLanguagesForTranslate()
    {
        $languageCodes = $this->translator->getLanguagesForTranslate();
        $this->assertIsArray($languageCodes);
        $this->assertTrue(count($languageCodes) > 30);
        return $languageCodes;
    }

    public function testGetLanguageNames()
    {
        $languageCodes = $this->translator->getLanguagesForTranslate();

        $languageNames = $this->translator->getLanguageNames([], 'nl');

        foreach ($languageCodes as $languageCode) {
            $this->assertArrayHasKey($languageCode, $languageNames);
        }
    }

    private function getEnvironmentVariable($name)
    {
        if (getenv($name) === false) {
            $this->markTestSkipped(sprintf('Environment variable "%s" is missing', $name));
        }

        return getenv($name);
    }

    public function testDictionaryLookup()
    {
        $translated = $this->translator->dictionaryLookup('fly', 'es', 'en');

        $this->assertGreaterThanOrEqual(2, $translated);
        $this->assertSame('volar', $translated[0]['normalizedTarget']);
        $this->assertSame('volar', $translated[0]['displayTarget']);
        $this->assertSame('VERB', $translated[0]['posTag']);
        $this->assertGreaterThanOrEqual(2, $translated[0]['backTranslations']);
    }

    public function testDictionaryLookupArray()
    {

        $translated = $this->translator->dictionaryLookupArray(['fly', 'dragonfly'], 'es', 'en');

        $this->assertGreaterThanOrEqual(2, $translated);
        $this->assertSame('volar', $translated[0][0]['normalizedTarget']);
        $this->assertSame('volar', $translated[0][0]['displayTarget']);
        $this->assertSame('VERB', $translated[0][0]['posTag']);
        $this->assertGreaterThanOrEqual(2, $translated[0][0]['backTranslations']);

        $this->assertSame('libélula', $translated[1][0]['normalizedTarget']);
        $this->assertSame('libélula', $translated[1][0]['displayTarget']);
        $this->assertSame('NOUN', $translated[1][0]['posTag']);
        $this->assertGreaterThanOrEqual(2, $translated[1][0]['backTranslations']);
    }


}
