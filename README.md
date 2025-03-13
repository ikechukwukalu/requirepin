# RequirePin

![Latest Version on Packagist](https://img.shields.io/packagist/v/ikechukwukalu/requirepin.svg?style=flat-square)
![Quality Score](https://img.shields.io/scrutinizer/g/ikechukwukalu/requirepin.svg?style=flat-square)
![Code Quality](https://img.shields.io/codefactor/grade/github/ikechukwukalu/requirepin/main?style=flat-square)
![Known Vulnerabilities](https://snyk.io/test/github/ikechukwukalu/requirepin/badge.svg?style=flat-square)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/ikechukwukalu/requirepin/Tests?style=flat-square)
![Total Downloads](https://img.shields.io/packagist/dt/ikechukwukalu/requirepin.svg?style=flat-square)
![GitHub Repo stars](https://img.shields.io/github/stars/ikechukwukalu/requirepin?style=social)
![GitHub issues](https://img.shields.io/github/issues/ikechukwukalu/requirepin.svg?style=flat-square)
![GitHub forks](https://img.shields.io/github/forks/ikechukwukalu/requirepin.svg?style=flat-square)
![License](https://img.shields.io/github/license/ikechukwukalu/requirepin.svg?style=flat-square)

**RequirePin** is a Laravel package that provides middleware to enforce PIN confirmation and validation before processing requests to specified routes, adding an extra layer of security to your application.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Applying Middleware](#applying-middleware)
  - [Routes](#routes)
- [Customization](#customization)
  - [Publishing Configuration](#publishing-configuration)
  - [Publishing Language Files](#publishing-language-files)
  - [Publishing Views](#publishing-views)
- [Reserved Keys for Payload](#reserved-keys-for-payload)
- [To Display Return Payload Within Blade](#to-display-return-payload-within-blade)
- [Security Considerations](#security-considerations)
- [Contributing](#contributing)
- [License](#license)

## Requirements

- PHP 7.3 or higher
- Laravel 8 or higher

## Installation

To install the package, run the following command:

```bash
composer require ikechukwukalu/requirepin
```

After installation, publish the migration files:

```bash
php artisan vendor:publish --tag=rp-migrations
```

Then, run the migrations:

```bash
php artisan migrate
```

Configure your `.env` file to use Redis for queue management:

```env
REDIS_CLIENT=predis
QUEUE_CONNECTION=redis
```

Finally, start the queue worker:

```bash
php artisan queue:work
```

## Configuration

**RequirePin** uses Redis to manage PIN confirmation queues efficiently. Ensure that your Redis server is properly configured and running.

## Usage

### Applying Middleware

To enforce PIN confirmation on specific routes, apply the `require.pin` middleware to those routes or route groups. For example:

```php
Route::middleware(['require.pin'])->group(function () {
    // Protected routes
});
```

### Routes

The package provides the following routes:

**API Routes:**

- `POST api/change/pin`: Endpoint to change the user's PIN.
- `POST api/pin/required/{uuid}`: Endpoint to confirm the PIN for a specific request.

**Web Routes:**

- `POST change/pin`: Endpoint to change the user's PIN.
- `POST pin/required/{uuid}`: Endpoint to confirm the PIN for a specific request.
- `GET change/pin`: Page to display the form for changing the PIN.
- `GET pin/required/{uuid?}`: Page to display the form for PIN confirmation.

**Note:** To receive JSON responses, add the `'Accept: application/json'` header to your requests.

## Reserved Keys for Payload

The following keys are reserved for use within the payload:

- `uuid` - Unique identifier for the PIN request.
- `pin` - The PIN value submitted by the user.
- `expires` - Expiration time for the PIN request.
- `signature` - Timestamp indicating when the PIN was verified.
- `return_payload`
- `pin_validation`

Ensure these keys are not overridden when handling the payload.

## To Display Return Payload Within Blade

To display the returned payload values within a Blade template, use:

```blade
@if (session('return_payload'))
    @php
        [$status, $status_code, $data] = json_decode(session('return_payload'), true);
    @endphp
    <div class="alert alert-{!! $status === 'fail' ? 'danger' : 'success' !!} m-5 text-center">
        {!! $data['message'] !!}
    </div>
@endif
```

You can customize this based on your application's needs.

## Security Considerations

- **PIN Policies:** Ensure that your application enforces strong PIN policies, such as minimum length and complexity requirements.
- **Rate Limiting:** Implement rate limiting on PIN confirmation endpoints to prevent brute-force attacks.
- **Secure Storage:** Store PINs securely using appropriate hashing algorithms.

## Contributing

Contributions are welcome! Please read the [contribution guidelines](CONTRIBUTING.md) before submitting a pull request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
