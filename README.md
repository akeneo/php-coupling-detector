Inspector
=========

Command tools to ease review and enforce quality of Akeneo PIM community and enterprise editions. 

Coupling, long story short
--------------------------

At the very beginning, we used only Bundles in Akeneo PIM and we encountered some issues to re-use business code which was too much coupled with Symfony Framework and Doctrine ORM.

Then we started to introduce Components to write our very new business code, to avoid large BC breaks we didn't extract all the business code from our existing bundles.

So we're face a new difficulty, the new Components may depends on several classes located to Bundles and it's "normal" because these classes should be located in Components.

The "where to put my code rule" is harder to follow and review, that's why there is an attempt to provide commands to automatically check coupling violations.

From Akeneo PIM 1.3, we've also introduced Akeneo namespace to extract several piece of code re-useable for other projects.

So basic rules are the following,
 - Akeneo/Component: should never use a Bundle, never use the namespace Pim, never use the namespace PimEnterprise
 - Akeneo/Bundle: should never use the namespace Pim, never use the namespace PimEnterprise
 - Pim/Component: should never use a Bundle, never use the namespace PimEnterprise
 - Pim/Bundle: should never use the namespace PimEnterprise
 - PimEnterprise/Component: should never use a Bundle
