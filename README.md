Coupling Detector
=================

A set of command to ease the review of Akeneo PIM community and enterprise editions and enforce quality. 

It relies on internal pieces of fabpot/php-cs-fixer to easily parse code and detect issues.

How to Use?
-----------

In your `https://github.com/akeneo/pim-community-dev` fork (or clone).

```
    php bin/coupling-detector pim-community-dev
```

Akeneo Coupling, Long Story Short
---------------------------------

At the very beginning, we used only Bundles in Akeneo PIM and we encountered some issues to re-use business code which was too much coupled with Symfony Framework and Doctrine ORM.

Then we started to introduce Components to write our very new business code, to avoid large BC breaks we didn't extract all the business code from our existing bundles.

So we're face a new difficulty, the new Components may depends on several classes located to Bundles and it's "normal" because these classes should be located in Components.

From Akeneo PIM 1.3, we've also introduced Akeneo namespace to extract several pieces of code re-useable for other projects.

The "where to put my code rule" is harder to follow and review, that's why there is an attempt to provide commands to automatically check coupling violations.

Namespace rules
---------------

 - Akeneo: should never use the namespace Pim or PimEnterprise
 - Pim: should never use the namespace PimEnterprise
 - PimEnterprise: -

Components vs Bundles rules
---------------------------

 - Component: should never use a Bundle
 - Bundle: -

Pim Bundles rules
-----------------

 - Pim/Bundle/CatalogBundle ...

Others
------

 - Deprecated uses
 - Services aliases uses, for instance, forbid, pim_something in a akeneo namespace
