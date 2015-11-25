<?php

namespace Akeneo\CouplingDetector\Domain;

/**
 * A node is an item that will be parsed to check if it respects the coupling rules.
 * It could be PHP class file or a Symfony YAML service definition for example.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Node implements NodeInterface
{
    /** @var string */
    private $subject;

    /** @var string */
    private $filepath;

    /** @var array */
    private $tokens = [];

    /** @var string */
    private $type;

    /**
     * Node constructor.
     *
     * @param array  $tokens
     * @param string $subject
     * @param string $filepath
     * @param string $type
     */
    public function __construct(array $tokens, $subject, $filepath, $type)
    {
        $this->tokens = $tokens;
        $this->subject = $subject;
        $this->filepath = $filepath;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }
}
