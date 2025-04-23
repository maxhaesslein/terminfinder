# Terminfinder

Session scheduling for TTRPG groups.

## Installation

Copy all files into a folder on your webserver and open the URL in a webbrowser. Requires PHP 8.0 or greater. The directory needs to be writeable by the webserver.

## Config

If you want to edit config options, create the file `custom/config.php` with the following content (create the `custom` folder if it does not exist):

```php
<?php

return [
	'language' => 'en', // can be 'en' or 'de'
	'default_priority' => 1, // 1 = lowest, 2 = middle, 3 = highest
	'debug' => false, // set to true to enable debug mode
];

```

## Changelog

### v.1.1.1

- changed default priority to "I don't need to attend"
- default priority can be set via config
- debug mode can be enabled via config

### v.1.1.0

- added optional 'Priority' dropdown
- small bugfixes and enhancements

### v.1.0.0

- first release
