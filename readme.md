# UnoServer

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Make sure you have libreoffice and [unoserver](https://github.com/unoconv/unoserver/#installation) installed on your system.

For a quick setup on linux:

``` bash
sudo add-apt-repository ppa:libreoffice/ppa
sudo apt install libreoffice
sudo pip install unoserver
```

For a quick setup on mac:

``` bash
brew install libreoffice libreoffice-language-pack
/Applications/LibreOffice.app/Contents/python -m pip install unoserver
```


Then install the package in your project:

``` bash
composer require happydemon/unoserver
```

### Configuration

Publish the config file so you can define server connections;

``` bash
php artisan vendor:publish --tag=unoserver
```
This will publish the `unoserver.php` config file.

If you're making use of the generated `unoserver` command to start a server, you'll need to define the path to your local libreoffice esecutable.
```dotenv
# For mac usually: /Applications/LibreOffice.app/Contents/MacOS/soffice
UNSORSERVER_EXEC_LIBRE=
```

When generating a server, the host could be `0.0.0.0` so it's accessible from outside, defaults to `127.0.0.1`.
When generating the convert command, host should be the IP of the server that is running `unoserver`, defaults to `127.0.0.1`.
```dotenv
UNOSERVER_HOST=
UNOSERVER_PORT=
```

This package will try to find the executables for you if not defined.
```dotenv
UNSORSERVER_EXEC_SERVER=
UNSORSERVER_EXEC_CONVERT=
```

### Set up
To get you started as quickly as possible you should run:

````shell
php artisan unoserver:helpers
````

This will generate 2 files in `base_path('bin')`:
- **unoserver:** let's you quickly start up a unoserver instance
- **unoconvert:** let's you correctly interact with a unoserver instance

Always make sure there is a `bin/unoserver` running.

## Usage
The `HappyDemon\UnoServer\Facades\UnoServer` facade can be used to connect to a server and send it documents to convert for us.

### Configuration

Connections are defined under the `unoserver.servers` config.

There are 2 types configuration;

#### Script
You can define the path to a bash script.

You can check the output from `php artisan unoserver:helpers -h` as an example for a command file.

````php
<?php

return [
    'servers' => [
        'script' => [
            'command' => '~/www/bin/unoconvert',
        ]
    ]
];
````

#### Connection
You can also define the server connection manually.

````php
<?php

return [
    'servers' => [
        'remote' => [
            // IP where the unoserver is running
            'interface' => '127.0.0.1',
            'port' => 2002,
        ]
    ]
];
````

When going this route, it's best practice to also configure `unoserver.executables.unoconvert` with the path to the system's unoconvert executable

### Converting

In order to convert we'll have to define which server we'll be connecting to and a local disk file path to the source file.

You can convert either documents or spreadsheets:

````php
use HappyDemon\UnoServer\Facades\UnoServer;

// Use the UnoServerFactory to create a Unoserver
$document = app(\HappyDemon\UnoServer\UnoServerFactory::class)
    ->connection('script')
    ->document(storage_path('myWordFile.docx'))

// Use the UnoServer facade to create a UnoServer
$document = UnoServer::connection('script')
    ->document(storage_path('myWordFile.docx'))

// Delete the source file after a successful conversion:   
$spreadsheet = UnoServer::connection('remote')
    ->spreadsheet(storage_path('mySheets.xlsx', true))
````

The next step is defining which format we'll be converting to and executing the call:

````php
$generatedFile = $document->toFormat('pdf')
    ->convert()
````

The `convert()` call returns a `Illuminate\Http\UploadedFile` object, which offers several options to store your file on any file system disk:

````php
<?php
$generatedFile->store('documents/rendered', ['disk' => 'public']);
$generatedFile->storePublicly('documents/rendered', ['disk' => 'public']);

$generatedFile->storeAs('documents/rendered','yourWordDoc.pdf', ['disk' => 'public']);
$generatedFile->storePubliclyAs('documents/rendered','yourWordDoc.pdf', ['disk' => 'public']);
````

### IOC bindings

| Class                                    | function |
|------------------------------------------|---------|
| `\HappyDemon\UnoServer\UnoServerFactory` | sdfsdf   |
| `\HappyDemon\UnoServer\UnoServer`        | sdfsdf  |

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email maxim.kerstens@gmail.com instead of using the issue tracker.

## Credits

- [Maxim Kerstens][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/happydemon/unoserver.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/happydemon/unoserver.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/happydemon/unoserver
[link-downloads]: https://packagist.org/packages/happydemon/unoserver
[link-author]: https://github.com/happydemon
[link-contributors]: ../../contributors
