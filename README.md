# Vat API

A simple class to get European VAT ID details (company name and address).

## Requirements

- PHP 5.6+
- Laravel 5.0+
- sunra/php-simple-html-dom-parser 1.5+
- guzzlehttp/guzzle 6.2+

## Installing

Use Composer to install it:

```
composer require filippo-toso/vat-api
```

If you are using Laravel < 5.5, you also need to:

1. register the service provider in the providers array of your config/app.php:

```
FilippoToso\VatApi\VatApiServiceProvider::class,
```

2. add the following row in the aliases array of the same config/app.php file:

```
'Vat'   => FilippoToso\VatApi\VatApiFacade::class,
```

## Using It

The use is very simple:

```
$details = Vat::details('LU26375245');
```
