<?php

namespace Akeneo\CouplingDetector\Console;

use Symfony\Component\Console\Formatter\OutputFormatter as BaseOutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Output formatter for coupling detector.
 * Just adds some styles to the Symfony output formatter.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class OutputFormatter extends BaseOutputFormatter
{
    /**
     * OutputFormatter constructor.
     *
     * @param bool  $decorated
     * @param array $styles
     */
    public function __construct($decorated = false, array $styles = array())
    {
        parent::__construct($decorated, $styles);

        $this->setStyle('passed', new OutputFormatterStyle('green', null, array()));
        $this->setStyle('passed-bg', new OutputFormatterStyle('black', 'green', array('bold')));

        $this->setStyle('broken', new OutputFormatterStyle('red', null, array()));
        $this->setStyle('broken-bg', new OutputFormatterStyle('white', 'red', array('bold')));

        $this->setStyle('blink', new OutputFormatterStyle(null, null, array('blink')));
    }
}
