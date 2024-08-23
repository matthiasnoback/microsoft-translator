# Changelog

All notable changes to this project will be documented in this file. See [commit-and-tag-version](https://github.com/absolute-version/commit-and-tag-version) for commit guidelines.

## [4.0.0](https://github.com/matthiasnoback/microsoft-translator/compare/v4.0-beta...v4.0.0) (2024-08-23)

### Features

* Added DictionaryLookupArray operation ([64a24fc](https://github.com/matthiasnoback/microsoft-translator/commit/64a24fcbbe4d2f7ce73af3ae97a1ea925afd22a2))
* Each operation has its own character and array element limits now ([64a24fc](https://github.com/matthiasnoback/microsoft-translator/commit/64a24fcbbe4d2f7ce73af3ae97a1ea925afd22a2))

### Fixes
* Increased operation limits ([811a984](https://github.com/matthiasnoback/microsoft-translator/commit/811a9844845a9c6e77a9a4a68d1844ca3a83fbfe))
* Removed deprecation notice ([1d25327](https://github.com/matthiasnoback/microsoft-translator/commit/1d253278c572111269ad837773e5b5d08dd0d065))

## [4.0-beta](https://github.com/matthiasnoback/microsoft-translator/releases/tag/v4.0-beta)
- Finally PHP 8.0 is supported now (PHP7 still supported)
- Added DictionaryLookup call
- Added PSR6 compatible Cache support
- Upgraded to Buzz Browser v1.2
  - Any PSR7 compatible adapter should work with it

- Some detected BC breaks
  - Dropped PHP5.6 compatibility
  - AzureTokenCache and CachedClient constructors changed to support PSR6 caches
    - This means Doctrine\Common\Cache\ArrayCache is no longer supported
  - Buzz Browser upgrade forces new initialization process 

