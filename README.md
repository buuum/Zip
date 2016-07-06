Library for PHP ZipArchive functions    
==============================

[![Packagist](https://img.shields.io/packagist/v/buuum/Zip.svg?maxAge=2592000)](https://packagist.org/packages/buuum/zip)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?maxAge=2592000)](#license)

## Install

### System Requirements

You need PHP >= 5.5.0 to use Buuum\Zip but the latest stable version of PHP is recommended.

### Composer

Buuum\Zip is available on Packagist and can be installed using Composer:

```
composer require buuum/zip
```

### Manually

You may use your own autoloader as long as it follows PSR-0 or PSR-4 standards. Just put src directory contents in your vendor directory.

##  INITIALIZE

```php
$zip_name = __DIR__ . '/demo.zip';
// Create a new zip
$zip = \Buuum\Zip\Zip::create($zip_name);
// Open an exist zip
$zip = \Buuum\Zip\Zip::open($zip_name);
// Check a zip archive
$zip = \Buuum\Zip\Zip::check($zip_name);
```

## ADD
Add files into zip
```php
$zip->add(__DIR__ . '/resources/README.md', 'resources/README.md');
$zip->add(__DIR__ . '/resources/info.txt', 'resources/info.txt');
$zip->add(__DIR__ . '/resources/composer/autoload_classmap.php', 'resources/ac.php');
// OR
$zip->setPath(__DIR__);
$zip->add(__DIR__ . '/resources/README.md');
$zip->add(__DIR__ . '/resources/info.txt');
$zip->add(__DIR__ . '/resources/composer/autoload_classmap.php', 'resources/ac.php');
```

## DELETE
```php
$zip->delete('resources/ac.php');
$zip->delete(['resources/README.md','resources/info.txt']);
```

## LIST
Return array list of files
```php
$zip->listFiles();
```

## EXTRACT
```php
$zip->extract(__DIR__.'/extract');
```

## CLOSE
```php
$zip->close();
```


## LICENSE

The MIT License (MIT)

Copyright (c) 2016

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.