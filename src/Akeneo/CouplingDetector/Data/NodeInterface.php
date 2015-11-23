<?php

namespace Akeneo\CouplingDetector\Data;

/**
 * A node is an item that will be parsed to check if it respects the coupling rules.
 * It could be PHP class file or a Symfony YAML service definition for example.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
interface NodeInterface
{
    const TYPE_PHP_USE = 'php_use';

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string $subject
     */
    public function setSubject($subject);

    /**
     * @return string
     */
    public function getFilepath();

    /**
     * @param string $filepath
     */
    public function setFilepath($filepath);

    /**
     * @return array
     */
    public function getTokens();

    /**
     * @param array $tokens
     */
    public function setTokens($tokens);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);
}
