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
];

```

## Changelog

### v.1.1.0

- added optional 'Priority' dropdown
- small bugfixes and enhancements

### v.1.0.0

- first release
