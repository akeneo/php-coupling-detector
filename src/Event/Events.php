<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Event;

/**
 * List of event dispatched by the coupling detector.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Events
{
    /**
     * The PRE_NODES_PARSED event occurs before nodes have been parsed.
     *
     * The event listener method receives a Akeneo\CouplingDetector\Event\PreNodesParsedEvent instance.
     *
     * @Event
     */
    const PRE_NODES_PARSED = 'php_cd.pre_nodes_parsed';

    /**
     * The NODE_PARSED event occurs when a node has been parsed.
     *
     * The event listener method receives a Akeneo\CouplingDetector\Event\NodeParsedEvent instance.
     *
     * @Event
     */
    const NODE_PARSED = 'php_cd.node_parsed';

    /**
     * The POST_NODES_PARSED event occurs when all nodes have been parsed.
     *
     * The event listener method receives a Akeneo\CouplingDetector\Event\PostNodesParsedEvent instance.
     *
     * @Event
     */
    const POST_NODES_PARSED = 'php_cd.post_nodes_parsed';

    /**
     * The PRE_RULES_CHECKED event occurs before all have been checked.
     *
     * The event listener method receives a Akeneo\CouplingDetector\Event\PreRulesCheckedEvent instance.
     *
     * @Event
     */
    const PRE_RULES_CHECKED = 'php_cd.pre_rules_checked';

    /**
     * The NODE_CHECKED event occurs when a node has been checked for a rule.
     *
     * The event listener method receives a Akeneo\CouplingDetector\Event\NodeChecked instance.
     *
     * @Event
     */
    const NODE_CHECKED = 'php_cd.node_checked';

    /**
     * The RULE_CHECKED event occurs when a rule has been checked for all nodes.
     *
     * The event listener method receives a Akeneo\CouplingDetector\Event\RuleCheckedEvent instance.
     *
     * @Event
     */
    const RULE_CHECKED = 'php_cd.rule_checked';

    /**
     * The POST_RULES_CHECKED event occurs when all rules have been checked for all nodes.
     *
     * The event listener method receives a Akeneo\CouplingDetector\Event\PostRulesCheckedEvent instance.
     *
     * @Event
     */
    const POST_RULES_CHECKED = 'php_cd.post_rules_checked';
}
