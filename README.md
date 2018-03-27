PHP Library for the Google Civic Information API
=====

Lorem Ipsum

How To Use
-----

```
composer require mchljams/gcia
```
Then in your php file.

```
$gcia = new Mchljams\Gcia\Gcia();
$gcia->setKey('YOURKEY');
```
or 

```
use \Mchljams\Gcia\Gcia;
$gcia = new Gcia();
```

Unit Tests
-----
**Run Unit Tests**

Change directory to the root of this package and run:

```
./vendor/bin/phpunit
```

**Run Unit Tests With HTML Coverage Report**

(xdebug required)

```
./vendor/bin/phpunit --coverage-html coverage
```

Code Linting
-----
**Using PHP_CodeSniffer**

Developed using the PSR-2 Standard

```
./vendor/bin/phpcs --standard=PSR2 ./src
```
