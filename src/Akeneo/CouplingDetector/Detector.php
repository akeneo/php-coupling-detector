<?php

namespace Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Coupling\UseViolations;
use Akeneo\CouplingDetector\FilesReader;
use Akeneo\CouplingDetector\RulesApplier;
use Akeneo\CouplingDetector\TokensExtractor\ClassNameExtractor;
use Akeneo\CouplingDetector\TokensExtractor\NamespaceExtractor;
use Akeneo\CouplingDetector\TokensExtractor\UseDeclarationsExtractor;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Detect coupling use violations in a set of files by using rules
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Detector
{
    /**
     * @param FilesReader  $reader
     * @param RulesApplier $applier
     *
     * @return UseViolations
     */
    public function detectUseViolations(FilesReader $reader, RulesApplier $applier)
    {
        $couplingViolations = [];
        $namespaceExtractor = new NamespaceExtractor();
        $classnameExtractor = new ClassNameExtractor();

        foreach ($reader->read() as $file) {
            $content = $file->getContents();
            $tokens = Tokens::fromCode($content);
            $classNamespace = $namespaceExtractor->extract($tokens);
            $className      = $classnameExtractor->extract($tokens);

            $classFullName  = sprintf('%s\%s', $classNamespace, $className);
            $useDeclarationExtractor = new UseDeclarationsExtractor();
            $useDeclarations = $useDeclarationExtractor->extract($tokens);
            $useViolations = $applier->apply($classFullName, $useDeclarations);
            if (count($useViolations) > 0) {
                $couplingViolations[$classFullName] = $useViolations;
            }
        }

        return new UseViolations($couplingViolations);
    }
}