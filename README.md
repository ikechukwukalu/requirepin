# REQUIRE PIN

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ikechukwukalu/requirepin?style=flat-square)](https://packagist.org/packages/ikechukwukalu/requirepin)
[![Quality Score](https://img.shields.io/scrutinizer/quality/g/ikechukwukalu/requirepin/main?style=flat-square)](https://scrutinizer-ci.com/g/ikechukwukalu/requirepin/)
[![Code Quality](https://img.shields.io/codefactor/grade/github/ikechukwukalu/requirepin?style=flat-square)](https://www.codefactor.io/repository/github/ikechukwukalu/requirepin)
[![Known Vulnerabilities](https://snyk.io/test/github/ikechukwukalu/requirepin/badge.svg?style=flat-square)](https://security.snyk.io/package/composer/ikechukwukalu%2Frequirepin)
[![Github Workflow Status](https://img.shields.io/github/actions/workflow/status/ikechukwukalu/requirepin/requirepin.yml?branch=main&style=flat-square)](https://github.com/ikechukwukalu/requirepin/actions/workflows/requirepin.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ikechukwukalu/requirepin?style=flat-square)](https://packagist.org/packages/ikechukwukalu/requirepin)
[![GitHub Repo stars](https://img.shields.io/github/stars/ikechukwukalu/requirepin?style=flat-square)](https://github.com/ikechukwukalu/requirepin/stargazers)
[![GitHub issues](https://img.shields.io/github/issues/ikechukwukalu/requirepin?style=flat-square)](https://github.com/ikechukwukalu/requirepin/issues)
[![GitHub forks](https://img.shields.io/github/forks/ikechukwukalu/requirepin?style=flat-square)](https://github.com/ikechukwukalu/requirepin/forks)
[![Licence](https://img.shields.io/packagist/l/ikechukwukalu/requirepin?style=flat-square)](https://github.com/ikechukwukalu/requirepin/blob/main/LICENSE.md)

A simple Laravel package that provides a middleware which will require users to confirm routes utilizing their pin for authentication.

## REQUIREMENTS

- PHP 7.3+
- Laravel 8+

## STEPS TO INSTALL

``` shell
composer require ikechukwukalu/requirepin
```

- `php artisan vendor:publish --tag=rp-migrations`
- `php artisan migrate`
- Set `REDIS_CLIENT=predis` and `QUEUE_CONNECTION=redis` within your `.env` file.
- `php artisan queue:work`

## ROUTES

### Api routes

- **POST** `api/change/pin`
- **POST** `api/pin/required/{uuid}`

### Web routes

- **POST** `change/pin`
- **POST** `pin/required/{uuid}`
- **GET** `change/pin`
- **GET** `pin/required/{uuid?}`

## NOTE

- To receive json response add `'Accept': 'application/json'` to your headers.

## HOW IT WORKS

- First, it's like eating candy.
- The `require.pin` middlware should be added to a route or route group.
- This middleware will arrest all incoming requests.
- A temporary URL (`pin/required/{uuid}`) is generated for a user to authenticate with the specified input `config(requirepin.input)` using their pin.
- It either returns a `JSON` response with the generated URL or it redirects to a page where a user is required to authenticate the request by entering their pin into a form that will send a **POST** request to the generated URL when submitted.
- To display return payload within blade:

```js
@if (session('return_payload'))
    @php
        [$status, $status_code, $data] = json_decode(session('return_payload'), true);
    @endphp
    <div class="alert alert-{!! $status === 'fail' ? 'danger' : 'success' !!} m-5 text-center">
        {!! $data['message'] !!}
    </div>
@endif
```

### Reserved keys for payload

- `_uuid`
- `_pin`
- `expires`
- `signature`
- `return_payload`
- `pin_validation`

## PUBLISH CONFIG

- `php artisan vendor:publish --tag=rp-config`

## PUBLISH LANG

- `php artisan vendor:publish --tag=rp-lang`

## PUBLISH VIEWS

- `php artisan vendor:publish --tag=rp-views`

## LICENSE

The RP package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
