# PHP library for the Microsoft Translator V2 API

By Matthias Noback

[![Build Status](https://travis-ci.org/matthiasnoback/microsoft-translator.png?branch=master)](https://travis-ci.org/matthiasnoback/microsoft-translator) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/matthiasnoback/microsoft-translator/badges/quality-score.png?s=a3230ce4a66715d3a62793da48ba24d8a30ab85d)](https://scrutinizer-ci.com/g/matthiasnoback/microsoft-translator/) [![Dependency Status](https://www.versioneye.com/user/projects/57682da5fdabcd003c031381/badge.svg?style=flat)](https://www.versioneye.com/user/projects/57682da5fdabcd003c031381)

## Installation

Using Composer, add to ``composer.json``:

    {
        "require": {
            "matthiasnoback/microsoft-translator": "dev-master"
        }
    }

Then using the Composer binary:

    php composer.phar install

## Usage

This library uses the Buzz browser to make calls to the [Microsoft Translator V2 API](http://msdn.microsoft.com/en-us/library/ff512419.aspx).

You need to [obtain a Microsoft Azure Cognitive Services subscription Key](http://docs.microsofttranslator.com/text-translate.html). This can be used to instantiate the ``AzureTokenProvider``:

```php
<?php

use Buzz\Browser;
use MatthiasNoback\MicrosoftOAuth\AzureTokenProvider;
use MatthiasNoback\MicrosoftTranslator\MicrosoftTranslator;

$browser = new Browser();

$azureKey = '[YOUR-AZURE-SUBSCRIPTION-KEY]';

$accessTokenProvider = new AzureTokenProvider($browser, $azureKey);

$translator = new MicrosoftTranslator($browser, $accessTokenProvider);
```

## Azure DataMarket token usage [deprecated]

You need to register your application at the [Azure DataMarket](https://datamarket.azure.com/developer/applications) and
thereby retrieve a "client id" and a "client secret". These can be used to instantiate the ``AccessTokenProvider`` (deprecated) on which
the ``MicrosoftTranslator`` depends:

```php
<?php

use Buzz\Browser;
use MatthiasNoback\MicrosoftOAuth\AccessTokenProvider;
use MatthiasNoback\MicrosoftTranslator\MicrosoftTranslator;

$browser = new Browser();

$clientId = '[YOUR-CLIENT-ID]';
$clientSecret = '[YOUR-CLIENT-SECRET]';

$accessTokenProvider = new AccessTokenProvider($browser, $clientId, $clientSecret);

$translator = new MicrosoftTranslator($browser, $accessTokenProvider);
```


### Optional: enable the access token cache

Each call to the translator service is preceded by a call to Microsoft's OAuth server. Each access token however, may be
cached for 10 minutes, so you should also use the built-in ``AccessTokenCache``:

```php
<?php

use MatthiasNoback\MicrosoftOAuth\AccessTokenCache;
use Doctrine\Common\Cache\ArrayCache;

$cache = new ArrayCache();
$accessTokenCache = new AccessTokenCache($cache);
$accessTokenProvider->setCache($accessTokenCache);
```

The actual cache provider can be anything, as long as it implements the ``Cache`` interface from the Doctrine Common library.

## Making calls

### Translate a string

```php
$translatedString = $translator->translate('This is a test', 'nl', 'en');

// $translatedString will be 'Dit is een test', which is Dutch for...
```

### Translate a string and get multiple translations

```php
$matches = $translator->getTranslations('This is a test', 'nl', 'en');

foreach ($matches as $match) {
    // $match is an instance of MatthiasNoback\MicrosoftTranslator\ApiCall\TranslationMatch
    $degree = $match->getDegree();
    $translatedText = $match->getTranslatedText();
}
```

### Detect the language of a string

```php
$text = 'This is a test';

$detectedLanguage = $translator->detect($text);

// $detectedLanguage will be 'en'
```

### Get a spoken version of a string

```php
$text = 'My name is Matthias';

$spoken = $translator->speak($text, 'en', 'audio/mp3', 'MaxQuality');

// $spoken will be the raw MP3 data, which you can save for instance as a file
```

## Tests

Take a look at the tests to find out what else you can do with the API.

To fully enable the test suite, you need to copy ``phpunit.xml.dist`` to ``phpunit.xml`` and replace the placeholder
values with their real values (i.e. client id, client secret and a location for storing spoken text files).

[![Build Status](https://secure.travis-ci.org/matthiasnoback/microsoft-translator.png)](http://travis-ci.org/matthiasnoback/microsoft-translator)

## Related projects

There is a [MicrosoftTranslatorBundle](https://github.com/matthiasnoback/MicrosoftTranslatorBundle) which makes the Microsoft translator available in a Symfony2 project.

There is also a [MicrosoftTranslatorServiceProvider](https://github.com/matthiasnoback/MicrosoftTranslatorServiceProvider) which registers the Microsoft translator and related services to a Silex application.

## TODO

There are some more calls to be implemented, and also some more tests to be added.
