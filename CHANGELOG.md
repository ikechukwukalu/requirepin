# v1.0.6

- Updated package to support Laravel 8
- Added settings for custom auth guard - `middleware('auth_route_guard')`  and `Auth::guard()->check('auth_guard')`
- Fixed middleware bug
- Fixed max trial bug

## v1.0.5

- Corrected typos

## v1.0.4

- Return correct status code from server for ajax requests

## v1.0.3

- Removed static functions and switched to facade for middleware class

## v1.0.2

- Removed `Books` migration file

## v1.0.1

- Removed `BookController` controller class
- Removed `Book` model class
- Removed `createBookRequest` request class
- Removed `php artisan sample:routes` command
