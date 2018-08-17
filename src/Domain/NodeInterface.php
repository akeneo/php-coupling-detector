<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Domain;

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
     * @return string
     */
    public function getFilepath();

    /**
     * @return array
     */
    public function getTokens();

    /**
     * @return string
     */
    public function getType();
}
