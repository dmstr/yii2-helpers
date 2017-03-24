Yii2 Helpers
============

[![Latest Stable Version](https://poser.pugx.org/dmstr/yii2-helpers/v/stable.svg)](https://packagist.org/packages/dmstr/yii2-helpers) 
[![Total Downloads](https://poser.pugx.org/dmstr/yii2-helpers/downloads.svg)](https://packagist.org/packages/dmstr/yii2-helpers)
[![License](https://poser.pugx.org/dmstr/yii2-helpers/license.svg)](https://packagist.org/packages/dmstr/yii2-helpers)

Yii2 Helpers

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist dmstr/yii2-helpers "*"
```

or add

```
"dmstr/yii2-helpers": "*"
```

to the require section of your `composer.json` file.


Usage
-----

### Metadata

Retrieve application meta-information, such as module or controller routes.



Testing
-------

Run the tests with phd testing-stack

    cd tests
    docker-compose up -d

Setup application

    docker-compose run --rm phpfpm setup.sh

Run test suites

    docker-compose run --rm -e YII_ENV=test phpfpm codecept run

or start a bash for 

    docker-compose run --rm -e YII_ENV=test phpfpm bash
