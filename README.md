# PHP Coupling Detector

[![Build Status](https://travis-ci.org/akeneo/php-coupling-detector.png)](https://travis-ci.org/akeneo/php-coupling-detector)

The PHP Coupling Detector tool detects all the coupling issues of your project with respect to the coupling 
rules you have defined. 

At the moment, only PHP coupling issues are detected by analysing the use statements of the classes in your project. 
But adding a new kind of coupling detections is doable in the future. We could for example imagine to detect the 
 coupling issues of Symfony services that are defined in YAML or XML

At the moment, 3 types of rules are supported:
    * *forbidden*: A node respects such a rule if no rule token is present in the node. In case the node does not respect this rule, an error violation will be sent.
    * *discouraged*: A node respects such a rule if no rule token is present in the node. In case the node does not respect this rule, a warning violation will be sent.
    * *only*: A node respects such a rule if the node contains only tokens defined in the rule. In case the node does not respect this rule, an error violation will be sent.

## Requirements

PHP needs to be a minimum version of PHP 5.3.6.

## Installation

### Globally (Composer)

To install PHP-Coupling-Detector, install Composer and issue the following command:

.. code-block:: bash

    $ ./composer.phar global require akeneo/php-coupling-detector

Then, make sure you have ``~/.composer/vendor/bin`` in your ``PATH``, and
you're good to go:

.. code-block:: bash

    export PATH="$PATH:$HOME/.composer/vendor/bin"

## Usage

The detect command detects coupling problems for a given file or directory depending on the
 coupling rules that have been defined:
 
     php bin/coupling-detector detect /path/to/dir
     php bin/coupling-detector detect /path/to/file
 
 The exit status of the detect command can be: 0 if no violations have been raised, 10 in case of
 warnings and 99 in case of errors.
 
 You can save the configuration in a ``.php_cd`` file in the root directory of
 your project. The file must return an instance of ``Akeneo\CouplingDetector\Configuration\Configuration``,
 which lets you configure the rules and the directories that need to be analyzed.
 Here is an example below:
 
     <?php
     use \Akeneo\CouplingDetector\Domain\Rule;
     use \Akeneo\CouplingDetector\Domain\RuleInterface;
 
     $finder = new \Symfony\Component\Finder\Finder();
     $finder
         ->files()
         ->name('*.php')
         ->notPath('foo/bar/');
 
     $rules = [
         new Rule('foo', ['bar', 'baz'], RuleInterface::TYPE_FORBIDDEN),
         new Rule('zoo', ['too'], RuleInterface::TYPE_DISCOURAGED),
         new Rule('bli', ['bla', 'ble', 'blu'], RuleInterface::TYPE_ONLY),
     ];
 
     return new \Akeneo\CouplingDetector\Configuration\Configuration($rules, $finder);
     ?>
 
 You can also use the default finder implementation if you want to analyse all the PHP files
 of your directory:
 
     <?php
     use \Akeneo\CouplingDetector\Domain\Rule;
     use \Akeneo\CouplingDetector\Domain\RuleInterface;
 
     $rules = [
         new Rule('foo', ['bar', 'baz'], RuleInterface::TYPE_FORBIDDEN),
         new Rule('zoo', ['too'], RuleInterface::TYPE_DISCOURAGED),
         new Rule('bli', ['bla', 'ble', 'blu'], RuleInterface::TYPE_ONLY),
     ];
 
     return new \Akeneo\CouplingDetector\Configuration\Configuration(
         $rules,
         \Akeneo\CouplingDetector\Configuration\DefaultFinder
     );
     ?>
 
 With the ``--config-file`` option you can specify the path to the ``.php_cd`` file:
 
     php bin/coupling-detector detect /path/to/dir --config-file=/path/to/my/configuration.php_cd
