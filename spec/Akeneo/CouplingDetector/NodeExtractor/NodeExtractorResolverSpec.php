<?php

namespace spec\Akeneo\CouplingDetector\NodeExtractor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NodeExtractorResolverSpec extends ObjectBehavior
{
    function it_resolves_php_files()
    {
        $file = new \SplFileInfo(__FILE__);
        $this->resolve($file)->shouldReturnAnInstanceOf('Akeneo\CouplingDetector\NodeExtractor\PhpClassNodeExtractor');
    }

    function it_does_not_resolve_non_supported_files()
    {
        $file = new \SplFileInfo(__DIR__ . '/../../../../composer.json');
        $this->resolve($file)->shouldReturn(null);
    }
}
