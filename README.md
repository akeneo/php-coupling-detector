# PHP Coupling Detector

The PHP Coupling Detector tool detects all the coupling issues of your project with respect to the coupling 
rules you have defined. 

At the moment, only PHP coupling issues are detected by analysing the use statements of the classes in your project. 
But adding a new kind of coupling detections is doable in the future. We could for example imagine to detect the 
 coupling issues of Symfony services that are defined in YAML or XML

At the moment, 3 types of rules are supported:

* _forbidden_: A node respects such a rule if no rule token is present in the node. In case the node does not respect this rule, an error violation will be sent.
* _discouraged_: A node respects such a rule if no rule token is present in the node. In case the node does not respect this rule, a warning violation will be sent.
* _only_: A node respects such a rule if the node contains only tokens defined in the rule. In case the node does not respect this rule, an error violation will be sent.

## Requirements

PHP needs to be a minimum version of PHP 7.2

## Installation

```bash
    $ composer require akeneo/php-coupling-detector
```

## Usage

To discover how to use this tool, please read the usage of the [detect](doc/DETECT.md) and [list-unused-requirements](doc/LIST_UNUSED_REQUIREMENTS.md) commands.

## Development

You can develop out of the box thanks to the provided `docker-compose.yml` and `Makefile` files 

To install the app:

```bash
    $ make vendor
```

To launch tests on your machine:

```bash
    $ make test
```
