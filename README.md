# REQUIRE PIN

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ikechukwukalu/requirepin?style=flat-square)](https://packagist.org/packages/ikechukwukalu/requirepin)
[![Quality Score](https://img.shields.io/scrutinizer/quality/g/ikechukwukalu/requirepin/main?style=flat-square)](https://scrutinizer-ci.com/g/ikechukwukalu/requirepin/)
[![Github Workflow Status](https://img.shields.io/github/actions/workflow/status/ikechukwukalu/requirepin/requirepin.yml?branch=main&style=flat-square)](https://github.com/ikechukwukalu/requirepin/actions/workflows/requirepin.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ikechukwukalu/requirepin?style=flat-square)](https://packagist.org/packages/ikechukwukalu/requirepin)
[![Licence](https://img.shields.io/packagist/l/ikechukwukalu/requirepin?style=flat-square)](https://github.com/ikechukwukalu/requirepin/blob/main/LICENSE.md)

A simple Laravel package that provides a middleware which will require users to confirm routes utilizing their pin for authentication.

## REQUIREMENTS

- PHP 8.0+
- Laravel 9+

## STEPS TO INSTALL

``` shell
composer require ikechukwukalu/requirepin
```

- `php artisan vendor:publish --tag=rp-migrations`
- `php artisan migrate`

## NOTE

- To receive json response add `'Accept': 'application/json'` to your headers.
- To aid your familiarity with this package you can run `php artisan sample:routes` to scaffold routes that will call functions within the `BookController`.

## PUBLISH CONFIG

- `php artisan vendor:publish --tag=rp-config`

## PUBLISH LANG

- `php artisan vendor:publish --tag=rp-lang`

## PUBLISH VIEWS

- `php artisan vendor:publish --tag=rp-view`

## LICENSE

The RP package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
