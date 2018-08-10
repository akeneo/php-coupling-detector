<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\Violation;
use Akeneo\CouplingDetector\Domain\ViolationInterface;

/**
 * Check if a node respects or matches a rule.
 *
 * There are 3 types or rules at the moment:
 *   -"forbidden": A node respects such a rule if no rule token is present in the node. In case the node does not
 *                 respect this rule, an error violation will be sent.
 *   -"discouraged": A node respects such a rule if no rule token is present in the node. In case the node does not
 *                  respect this rule, a warning violation will be sent.
 *   -"only": A node respects such a rule if the node contains only tokens defined in the rule. In case the node
 *            does not respect this rule, an error violation will be sent.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class RuleChecker
{
    /**
     * Does a node match a rule?
     */
    public function match(RuleInterface $rule, NodeInterface $node): bool
    {
        if (false !== strpos($node->getSubject(), $rule->getSubject())) {
            return true;
        }

        return false;
    }

    /**
     * Checks if a node respect a rule.
     */
    public function check(RuleInterface $rule, NodeInterface $node): ?ViolationInterface
    {
        if (!$this->match($rule, $node)) {
            return null;
        }

        switch ($rule->getType()) {
            case RuleInterface::TYPE_FORBIDDEN:
            case RuleInterface::TYPE_DISCOURAGED:
                $violation = $this->checkForbiddenOrDiscouragedRule($rule, $node);
                break;
            case RuleInterface::TYPE_ONLY:
                $violation = $this->checkOnlyRule($rule, $node);
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown rule type "%s".', $rule->getType()));
        }

        return $violation;
    }

    /**
     * Checks if a node respects a "forbidden" or "discouraged" rule.
     * A node respects such a rule if no rule token is present in the node.
     */
    private function checkForbiddenOrDiscouragedRule(RuleInterface $rule, NodeInterface $node): ?ViolationInterface
    {
        $errors = array();

        foreach ($node->getTokens() as $token) {
            if (!$this->checkTokenForForbiddenOrDiscouragedRule($rule, $token) &&
                !in_array($token, $errors)) {
                $errors[] = $token;
            }
        }

        if (count($errors)) {
            $type = $rule->getType() === RuleInterface::TYPE_FORBIDDEN ?
                    ViolationInterface::TYPE_ERROR :
                    ViolationInterface::TYPE_WARNING
            ;

            return new Violation($node, $rule, $errors, $type);
        }

        return null;
    }

    /**
     * Checks if a token fits a "forbidden" / "discouraged" rule or not.
     */
    private function checkTokenForForbiddenOrDiscouragedRule(RuleInterface $rule, string $token): bool
    {
        foreach ($rule->getRequirements() as $req) {
            if (strpos($token, $req) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if a node respects a "only" rule.
     * A node respects such a rule if the node contains only tokens defined in the rule.
     */
    private function checkOnlyRule(RuleInterface $rule, NodeInterface $node): ?Violation
    {
        $errors = array();

        foreach ($node->getTokens() as $token) {
            if (!$this->checkTokenForOnlyRule($rule, $token) &&
                !in_array($token, $errors)) {
                $errors[] = $token;
            }
        }

        if (count($errors)) {
            return new Violation($node, $rule, $errors, ViolationInterface::TYPE_ERROR);
        }

        return null;
    }

    /**
     * Checks if a token fits a "only" rule or not.
     */
    private function checkTokenForOnlyRule(RuleInterface $rule, $token): bool
    {
        $fitRuleRequirements = false;
        foreach ($rule->getRequirements() as $req) {
            if (false !== strpos($token, $req)) {
                $fitRuleRequirements = true;
            }
        }

        return $fitRuleRequirements;
    }
}
