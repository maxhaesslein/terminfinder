# Terminfinder

Session scheduling for TTRPG groups

## Installation

Copy all files into a folder on your webserver. Requires PHP 8.0 or greater. The folder `data/` needs to be writeable.

## Config

If you want to edit config options, create the file `custom/config.php` with the following content:

```php
<?php

return [
	'language' => 'en', // can be 'en' or 'de'
];

```
