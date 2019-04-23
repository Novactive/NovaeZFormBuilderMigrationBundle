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
        new Novactive\Bundle\EzFormBuilderMigrationBundle\EzFormBuilderMigrationBundle(),
        // ...
    );
}
```

## Usage

Run the command following command to re-create eZStudio Form builder forms as content. 

Contents are created as children of the location defined by the parameter `form_builder.forms_location_id`. 

The `content_type_identifier` and `field_identifier` command options allow to change which content type is used to create the contents.

```bash
Usage:
  form_builder_migration:migrate [options]

Options:
      --content_type_identifier[=CONTENT_TYPE_IDENTIFIER]  Identifier for the content type to use for creating content [default: "form"]
      --field_identifier[=FIELD_IDENTIFIER]                Identifier for the content type field to use for storing the forms [default: "form"]
      --dry-run                                            When specified, changes are _NOT_ persisted to database.
  -h, --help                                               Display this help message
  -q, --quiet                                              Do not output any message
  -V, --version                                            Display this application version
      --ansi                                               Force ANSI output
      --no-ansi                                            Disable ANSI output
  -n, --no-interaction                                     Do not ask any interactive question
  -e, --env=ENV                                            The Environment name. [default: "dev"]
      --no-debug                                           Switches off debug mode.
      --siteaccess[=SITEACCESS]                            SiteAccess to use for operations. If not provided, default siteaccess will be used
  -v|vv|vvv, --verbose                                     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```
