# Changelog 4.0
## General
 * Bug Fixes and Performance / Security Improvments.
 * Error handlers strict compliance with PHP7+ (Replaced `Exception` with `Throwable`)
## Encryption
 * Deprecated Mcrypt Class (deprecated since PHP 7.1 - removed in PHP 7.2).
 * Add Support for Libsodium.
 * OpenSSL is now default engine.
 * `var_dump`ing an Engine won't display the Encryption Key anymore.
## ORM
 * `ORM->changed()` had unexpected behavior (returns value). Added function `ORM->has_changed()` which returns bool.
 * Added Support for non auto-increment Primary Keys
## Database
 * Added Support for `stdClass` attributes
 * Added JSON field type to `MySQLi` Driver
## Cache
 * Removed `memcache` and `apc` driver since both are removed wit PHP 7.0 (use `memcached` and `apcu` instead).
 * Also removed `MemcacheTag` Class as it depended on `memcache`
## Unittests
 * Added Enviroment variable `TRAVIS_TEST` which can be used to overwrite configurations for automated tests.
 * Added the following services which can be used for unittesting: redis, memcached, mysql
 * Added the following PHP-Extensions which can be used for unittesting: memcached, redis, imagick, apcu
 * And of course: Added more Unittests to improve Framework Code Coverage
## Userguide
 * Added Support for namespaced classes
