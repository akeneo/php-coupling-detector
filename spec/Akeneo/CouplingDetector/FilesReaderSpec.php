<?php

namespace spec\Akeneo\CouplingDetector;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FilesReaderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['src']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Akeneo\CouplingDetector\FilesReader');
    }

    function it_reads_files()
    {
        $this->read()->shouldReturnAnInstanceOf('\IteratorAggregate');
    }
}
