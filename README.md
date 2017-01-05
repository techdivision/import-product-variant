# M2IF - Configurable Product Import

[![Latest Stable Version](https://img.shields.io/packagist/v/techdivision/import-product-variant.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-product-variant) 
 [![Total Downloads](https://img.shields.io/packagist/dt/techdivision/import-product-variant.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-product-variant)
 [![License](https://img.shields.io/packagist/l/techdivision/import-product-variant.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-product-variant)
 [![Build Status](https://img.shields.io/travis/techdivision/import-product-variant/master.svg?style=flat-square)](http://travis-ci.org/techdivision/import-product-variant)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/techdivision/import-product-variant/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-product-variant/?branch=master) [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/techdivision/import-product-variant/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-product-variant/?branch=master)

 ## Introduction

This module provides the functionality to import the product variants defined in the CSV file.

## Configuration

In case that the [M2IF - Simple Console Tool](https://github.com/techdivision/import-cli-simple) 
is used, the funcationality can be enabled by adding the following snippets to the configuration 
file

```json
{
  "magento-edition": "CE",
  "magento-version": "2.1.2",
  "operation-name" : "replace",
  "installation-dir" : "/var/www/magento",
  "utility-class-name" : "TechDivision\\Import\\Utils\\SqlStatements",
  "database": { ... },
  "operations" : [
    {
      "name" : "replace",
      "subjects": [
        { ... },
        {
          "class-name": "TechDivision\\Import\\Product\\Variant\\Subjects\\VariantSubject",
          "processor-factory" : "TechDivision\\Import\\Cli\\Services\\ProductVariantProcessorFactory",
          "utility-class-name" : "TechDivision\\Import\\Product\\Variant\\Utils\\SqlStatements",
          "prefix": "variants",
          "source-dir": "projects/sample-data/tmp",
          "target-dir": "projects/sample-data/tmp",
          "observers": [
            {
              "import": [
                "TechDivision\\Import\\Product\\Variant\\Observers\\VariantObserver",
                "TechDivision\\Import\\Product\\Variant\\Observers\\VariantSuperAttributeObserver"
              ]
            }
          ]
        }
      ]
    },
    {
      "name" : "add-update",
      "subjects": [
        { ... },
        {
          "class-name": "TechDivision\\Import\\Product\\Variant\\Subjects\\VariantSubject",
          "processor-factory" : "TechDivision\\Import\\Cli\\Services\\ProductVariantProcessorFactory",
          "utility-class-name" : "TechDivision\\Import\\Product\\Variant\\Utils\\SqlStatements",
          "prefix": "variants",
          "source-dir": "projects/sample-data/tmp",
          "target-dir": "projects/sample-data/tmp",
          "observers": [
            {
              "import": [
                "TechDivision\\Import\\Product\\Variant\\Observers\\VariantUpdateObserver",
                "TechDivision\\Import\\Product\\Variant\\Observers\\VariantSuperAttributeUpdateObserver"
              ]
            }
          ]
        }
      ]
    }
  ]
}
```