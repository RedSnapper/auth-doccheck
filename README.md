# Very short description of the package
With the DocCheck Login you are able to very simply establish a protected area on your site, which is only
accessible to (medical) professionals The DocCheck password protection is an identification service for
medical sites which is compliant with the German law for pharmaceutical commercial information HWG
(Heilmittelwerbegesetz)

## Installation

You can install the package via composer:

```bash
composer require rs/auth-doccheck
```

## Configuration

Before using DocCheck, you will need to add credentials for the provider. 
```
DOCCHECK_LOGIN_ID=123
DOCCHECK_SECRET=my-secret
```
The doc check secret is optional and provides greater security.When acquiring the Economy or Business Licence you obtain a secret key from DocCheck.

If you need to overwrite the config, you can publish them using the vendor:publish Artisan command:

```bash
php artisan vendor:publish --tag=doccheck
```

## Authentication

DocCheck provides an iframe for allowing users to login using their service. This package allows for generation of the iframe url.

```php
use RedSnapper\DocCheck\DocCheckProvider;

Route::get('/login', function (DocCheckProvider $provider) {
    $provider->language("de")->template("login_s");
    return view('login',['url'=>$provider->iframeUrl()])
});
```
The iframe can be further configured to use which language and which template that is appropriate.
Available languages are de, com, fr, it, es, nl, frbe. Available templates are "login_s", "login_m", "login_l", "login_xl".

To authenticate users using the Doccheck, you will need a route for receiving the callback from the provider after authentication. The example controller below demonstrates the implementation:

It is also possible to generate the iframe markup directly.

```php
use RedSnapper\DocCheck\DocCheckProvider;

Route::get('/login', function (DocCheckProvider $provider) {
    return $provider->iframe();
});
```

```php
use RedSnapper\DocCheck\DocCheckProvider;

Route::get('/auth/callback', function (DocCheckProvider $provider) {
    $user = $provider->user();
});

```

The user method will read the incoming request and retrieve the user's information from the provider after they are authenticated.

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email param@redsnapper.net instead of using the issue tracker.

## Credits

-   [Param Dhaliwal](https://github.com/rs)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
