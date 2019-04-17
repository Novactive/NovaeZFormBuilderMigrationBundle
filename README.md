# Novactive eZ Form Builder Migration Bundle

[![Build Status](https://img.shields.io/travis/Novactive/NovaeZFormBuilderMigrationBundle.svg?style=flat-square&branch=master)](https://travis-ci.org/Novactive/NovaeZFormBuilderMigrationBundle)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZFormBuilderMigrationBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZFormBuilderMigrationBundle/releases)

## Requirements

- eZ Platform >= 2.4
- PHP >= 7.2

## Installation

### Use Composer

Add NovaeZFormBuilderMigrationBundle in your composer.json:

```bash
composer require novactive/ezformbuildermigrationbundle
```

### Register the bundle

Register the bundle in your application's kernel class:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Novactive\EzFormBuilderMigrationBundle\EzFormBuilderMigrationBundle(),
        // ...
    );
}
```

## Usage
