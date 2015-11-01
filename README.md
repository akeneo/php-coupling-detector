Coupling Detector
=================

A set of command to ease the review and enforce quality of Akeneo PIM community and enterprise editions.

It relies on internal pieces of fabpot/php-cs-fixer to easily parse code and detect issues.

It extracts use statements from classes and apply several rules to detect if a class introduces a forbidden coupling.

The command execution must be efficient to be able to be launched on Travis CI.

PS1: This tool in a very early stage of development

PS2: here is a first attempt to use the checker https://github.com/akeneo/pim-community-dev/pull/3457

How to Use?
-----------

In your `https://github.com/akeneo/pim-community-dev` fork (or clone).

```
    php bin/coupling-detector pim-community-dev
```

You can use `--strict` option to display all violations (by default, it exclude legacy violations).

You can use `--output` option to display,

 - `default` to display the list of classes with their use statement violations
 - `count` to display the count of use for the list of forbidden use statements
 - `none` to display nothing (to use only the command result)

Akeneo Coupling, Long Story Short
---------------------------------

At the very beginning, we used only Bundles in Akeneo PIM and we encountered some issues to re-use business code which was too much coupled with Symfony Framework and Doctrine ORM.

Then we started to introduce Components to write our very new business code, to avoid large BC breaks we didn't extract all the business code from our existing bundles.

So we're face a new difficulty, the new Components may depends on several classes located to Bundles and it's "normal" because these classes should be located in Components.

From Akeneo PIM 1.3, we've also introduced Akeneo namespace to extract several pieces of code re-useable for other projects.

The "where to put my code rule" is harder to follow and review, that's why there is an attempt to provide commands to automatically check coupling violations.

Namespace rules
---------------

 - Akeneo should never use the namespace Pim or PimEnterprise
 - Pim should never use the namespace PimEnterprise
 - PimEnterprise: -

Components rules
----------------

 - Component should never use a Bundle, should never use Doctrine/ORM

Specific PIM Bundles rules
--------------------------

 - Pim/Bundle/CatalogBundle should not use any part of Pim/Bundle/EnrichBundle, etc
 - Rules are defined in Akeneo\CouplingDetector\Console\Command\PimCommunityCommand

Legacy Exclusions
-----------------

For now, we exclude several violations because they are legacy way of doing, once the code re-worked in a backward compatible way, we'll be able to drop them.

More to come?
-------------

 - Deprecated uses, we should take a look on https://github.com/sensiolabs-de/deprecation-detector
 - Services aliases uses, for instance, forbid, pim_something in a akeneo namespace
