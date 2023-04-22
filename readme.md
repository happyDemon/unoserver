# UnoServer

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

This package is a Laravel-specific wrapper around [unoserver](https://github.com/unoconv/unoserver) commands, these let you easily convert office documents, like you would in LibreOffice.

Helper commands are provided to get you set up quickly.

## Installation

Install the package in your project:

``` bash
composer require happydemon/unoserver && \
```

#### Vendor publish

You can run `vendor:publish --tag=unoserver` te publish the config file and the platform installers.

You can also be more specific in what you want to publish:

- `vendor:publish --tag=unoserver.config`
- `vendor:publish --tag=unoserver.platforms`


### Set up

To get started quickly we bundled install scripts, these got exported thanks to `vendor:publish`.

From your application's root directory you can run:

```shell
sh platforms/mac
sh platforms/ubuntu
```

For Mac it's important `homebrew` is installed, for Ubuntu `python3`

The script will install LibreOffice, unoserver & set up some `.env` variables.

### Sail



### Configuration

`vendor:publish` published the `unoserver.php` config file.

If you're making use of the generated `unoserver` command to start a server, you'll need to define the path to your local libreoffice esecutable.
```dotenv
# For mac usually: /Applications/LibreOffice.app/Contents/MacOS/soffice
UNSORSERVER_EXEC_LIBRE=
```
You should always provide the path to a python executable that supports unoserver:
```dotenv
# For mac usually: /Applications/LibreOffice.app/Contents/Resources/python
UNSORSERVER_EXEC_PYTHON=
```

**Important:** both `UNSORSERVER_EXEC_LIBRE` and `UNSORSERVER_EXEC_PYTHON` environment variables get configured automatically when you set up your environment with a [set-up script](#set-up).

## Commands

### make:unoserver-cmd
This command generates bash scripts to:
- start a unoserver
- send convert requests to that server

**arguments** 

It takes a servername as the only argument (this would be a server you defined in the `unoserver.servers.*` config), if not provided it will use the default server. 

*parameters*

| Option       |                                                         |
|--------------|---------------------------------------------------------|
| --ip=        | IP the unoserver is hosted on (defaults to `127.0.0.1`) |
| --port=      | Port the uno server is running on (defaults to `2002`)  |
| --unoserver  | Generate unoserver in the app's bin folder              |
| --unoconvert | Generate unoconvert in the app's bin folder             |

````shell
php artisan make:unoserver-cmd
````

This will generate 2 files in `base_path('bin')`:
- **unoserver:** lets you quickly start up a unoserver instance
- **unoconvert:** lets you correctly interact with a unoserver instance

If a server was defined as an argument, both files' name will be suffixed with that server name.

Always make sure there is a `bin/unoserver` running when developing locally.

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

### Converting

In order to convert we'll have to define which server we'll be connecting to and the local file path to the source file.

You can convert either documents or spreadsheets:

````php
use HappyDemon\UnoServer\Facades\UnoServer;

// Use the UnoServerFactory to create a Unoserver
$document = app(\HappyDemon\UnoServer\UnoServerFactory::class)
    ->connect('script')
    ->fromDocument(storage_path('myWordFile.docx'))

// Use the UnoServer facade to create a UnoServer
$document = UnoServer::connect('script')
    ->fromDocument(storage_path('myWordFile.docx'))

// Delete the source file after a successful conversion:   
$spreadsheet = UnoServer::connect('remote')
    ->fromSpreadsheet(storage_path('mySheets.xlsx', true))
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

| Class                                    | function                                                     |
|------------------------------------------|--------------------------------------------------------------|
| `\HappyDemon\UnoServer\UnoServerFactory` | Configures a `UnoServer` with a proper connection.           |
| `\HappyDemon\UnoServer\UnoServer`        | Object used to configure & convert documents & spreadsheets. |

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

[ico-version]: https://img.shields.io/packagist/v/happydemon/unoserver?include_prereleases
[ico-downloads]: https://img.shields.io/packagist/dt/happydemon/unoserver?label=downloads&style=social

[link-packagist]: https://packagist.org/packages/happydemon/unoserver
[link-downloads]: https://packagist.org/packages/happydemon/unoserver
[link-author]: https://github.com/happydemon
[link-contributors]: ../../contributors
