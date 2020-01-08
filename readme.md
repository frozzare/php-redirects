# Redirects

[![Build Status](https://travis-ci.org/frozzare/php-redirects.svg?branch=master)](https://travis-ci.org/frozzare/php-redirects)

Parse Netlify's _redirects file [format](https://www.netlify.com/docs/redirects/).

## Installation

```
composer require frozzare/redirects
```

## Usage

```php
use Frozzare\Redirects\Redirects;

// or redirects() function.
$redirects = new Redirects(__DIR__ . '/_redirects');

// return array of rules.
$redirects->rules();

// match url against rule.
$redirects->match($url);

// get url to redirect to.
$redirects->url($url);
```

More examples in the [`tests/RedirectsTest.php`](tests/RedirectsTest.php)

## License

MIT © [Fredrik Forsmo](https://github.com/frozzare)

