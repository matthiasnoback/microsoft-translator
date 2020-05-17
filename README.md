# PHP library for the Microsoft Translator V3 API

By Matthias Noback (maintained by Alayn Gortazar)

[![Build Status](https://travis-ci.org/matthiasnoback/microsoft-translator.png?branch=master)](https://travis-ci.org/matthiasnoback/microsoft-translator) 

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

This library uses the Buzz browser to make calls to the [Microsoft Translator Text API 3.0](https://docs.microsoft.com/en-us/azure/cognitive-services/translator/reference/v3-0-languages?tabs=curl).

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
### Selecting azure token provider url

By default the acesstoken will be retrieved from https://api.cognitive.microsoft.com/sts/v1.0/issueToken , a third parameter can be passed to the AzureTokenProvider to choose another source.

```php
<?php
// Some code

$accessTokenProvider = new AzureTokenProvider($browser, $azureKey, 'https://westus.api.cognitive.microsoft.com/sts/v1.0/issueToken');

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
<?php
// Some code

$translatedString = $translator->translate('This is a test', 'nl', 'en');

// $translatedString will be 'Dit is een test', which is Dutch for...
```

### Translate a string and get multiple translations

```php
<?php
// Some code

$matches = $translator->getTranslations('This is a test', 'nl', 'en');

foreach ($matches as $match) {
    // $match is an instance of MatthiasNoback\MicrosoftTranslator\ApiCall\TranslationMatch
    $degree = $match->getDegree();
    $translatedText = $match->getTranslatedText();
}
```

### Detect the language of a string

```php
<?php
// Some code

$text = 'This is a test';

$detectedLanguage = $translator->detect($text);

// $detectedLanguage will be 'en'
```

## Tests

Take a look at the tests to find out what else you can do with the API.

To fully enable the test suite, you need to copy ``phpunit.xml.dist`` to ``phpunit.xml`` and replace the placeholder
values with their real values (i.e. client id, client secret and a location for storing spoken text files).

[![Build Status](https://secure.travis-ci.org/matthiasnoback/microsoft-translator.png)](http://travis-ci.org/matthiasnoback/microsoft-translator)

## Related projects

There is a [MicrosoftTranslatorBundle](https://github.com/matthiasnoback/MicrosoftTranslatorBundle) which makes the Microsoft translator available in a Symfony2 project.

There is also a [MicrosoftTranslatorServiceProvider](https://github.com/matthiasnoback/MicrosoftTranslatorServiceProvider) which registers the Microsoft translator and related services to a Silex application.

## Differences with V2 library
 * Removed speak method: Speak API has been removed from the Microsoft Translator Text API on v3.0.
 * Removed getTranslations method: GetTranslations functionality has been removed from the Microsoft Translator Text API on v3.0.
 * Removed Datamarket token compatibility: Azure datamarket is not longer available so this has been removed from v3.x

## TODO

 * Make CachedClient work (now bypasses any SendRequest call)
 * There are some more calls to be implemented, and also some more tests to be added.
