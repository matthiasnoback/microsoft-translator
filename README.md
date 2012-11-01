# PHP library for the Microsoft Translator V2 API

By Matthias Noback

## Installation

Using Composer, add to ``composer.json``:

    {
        "require": {
            "matthiasnoback/microsoft-translator": "*"
        }
    }

Then using the Composer binary:

    php composer.phar install

## Usage

This library uses the Buzz browser to make calls to the [Microsoft Translator V2 API](http://msdn.microsoft.com/en-us/library/ff512419.aspx).

You need to register your application at the [Azure DataMarket](https://datamarket.azure.com/developer/applications) and
thereby retrieve a "client id" and a "client secret". These kan be used to instantiate the ``AccessTokenProvider`` on which
the ``MicrosoftTranslator`` depends:

    <?php

    use Buzz\Browser;
    use MatthiasNoback\MicrosoftOAuth\AccessTokenProvider;
    use MatthiasNoback\MicrosoftTranslator\MicrosoftTranslator;

    $browser = new Browser();

    $clientId = '[YOUR-CLIENT-ID]';
    $clientSecret = '[YOUR-CLIENT-SECRET]';

    $accessTokenProvider = new AccessTokenProvider($browser, $clientId, $clientSecret);

    $translator = new MicrosoftTranslator($browser, $accessTokenProvider);

### Optional: enable the access token cache

Each call to the translator service is preceded by a call to Microsoft's OAuth server. Each access token however, may be
cached for 10 minutes, so you should also use the built-in ``AccessTokenCache``:

    <?php

    use MatthiasNoback\MicrosoftOAuth\AccessTokenCache;
    use Doctrine\Common\Cache\ArrayCache;

    $cache = new ArrayCache();
    $accessTokenCache = new AccessTokenCache($cache);
    $accessTokenProvider->setCache($accessTokenCache);

The actual cache provider can be anything, as long as it implements the ``Cache`` interface from the Doctrine Common library.

## Making calls

### Translate a string

    $translatedString = $translator->translate('This is a test', 'nl', 'en');

    // $translatedString will be 'Dit is een test', which is Dutch for...

### Detect the language of a string

    $text = 'This is a test';

    $detectedLanguage = $translator->detect($text);

    // $detectedLanguage will be 'en'

### Get a spoken version of a string

    $text = 'My name is Matthias';

    $spoken = $translator->speak($text, 'en', 'audio/mp3', 'MaxQuality');

    // $spoken will be the raw MP3 data, which you can save for instance as a file

## Tests

Take a look at the tests to find out what else you can do with the API.

To fully enable the test suite, you need to copy ``phpunit.xml.dist`` to ``phpunit.xml`` and replace the placeholder
values with their real values (i.e. client id, client secret and a location for storing spoken text files).

[![Build Status](https://secure.travis-ci.org/matthiasnoback/microsoft-translator.png)](http://travis-ci.org/matthiasnoback/microsoft-translator)

## TODO

There are some more calls to be implemented, and also some more tests to be added. I am also working on a bundle for Symfony2,
which will make the translator available as a service and will take care of setting up the cache and the browser.
