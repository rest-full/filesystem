# Rest-full Filesystem

## About Rest-full Filesystem


Rest-full Filesystem is a small part of the Rest-Full framework.

You can find the application at: [rest-full/app](https://github.com/rest-full/app) and you can also see the framework skeleton at: [rest-full/rest-full](https://github.com/rest-full/rest-full).

## Installation

* Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
* Run `php composer.phar require rest-full/filesystem` or composer installed globally `compser require rest-full/filesystem` or composer.json `"rest-full/filesystem": "1.0.0"` and install or update.

## Usage

This File
```
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../config/pathServer.php';

use Restfull\Filesystem\File;

$file = new File(__DIR__ . '/files/ps4.txt');
$file->write('batman, dragon ball z kakarot e entre outros.');
$file->close();
```
This Upload
```
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../config/pathServer.php';

use Restfull\Filesystem\Upload;

$upload = new Upload([$_FILE['tmp_name']],500000);
echo $upload->insert('Here the file name.');
```
This Folder
```
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../config/pathServer.php';

use Restfull\Filesystem\Folder;

$folder = new Folder(__DIR__);
$folder->create('example');
```
## License

The rest-full framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

