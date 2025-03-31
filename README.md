# Terminfinder

Session scheduling for TTRPG groups

## Installation

Copy all files into a folder on your webserver. Requires PHP 8.0 or greater.

## Config

If you want to edit config options, create the file `custom/config.php` with the following content:

```php
<?php

return [
	'language' => 'en', // can be 'en' or 'de'
];

```

## Changelog

### v.1.1.0

- added optional 'Priority' dropdown
- small bugfixes and enhancements

### v.1.0.0

- first release
