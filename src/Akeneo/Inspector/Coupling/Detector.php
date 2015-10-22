<?php

namespace Akeneo\Inspector\Coupling;

use Symfony\Component\Finder\Finder;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Detect coupling violation in a namespace
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Detector
{
    /**
     * @param string $path
     * @param string $namespace
     * @param array  $forbiddenUses
     *
     * @return UseViolations
     */
    public function detectCoupling($path, $namespace, $forbiddenUses)
    {
        $couplingViolations = [];
        $finder = new Finder();
        $finder->files()->in($path)->name('*.php');

        $namespaceExtractor = new NamespaceExtractor();
        $classnameExtractor = new ClassNameExtractor();
        foreach ($finder as $file) {
            if (strpos($file->getRelativePath(), $namespace) === 0) {
                $content = $file->getContents();
                $tokens = Tokens::fromCode($content);
                $classNamespace = $namespaceExtractor->extract($tokens);
                $className = $classnameExtractor->extract($tokens);
                $classFullName = sprintf('%s\%s', $classNamespace, $className);
                $useDeclarationExtractor = new UseDeclarationsExtractor();
                $useDeclarations = $useDeclarationExtractor->extract($tokens);
                foreach ($useDeclarations as $useDeclaration) {
                    $useFullName = $useDeclaration['fullName'];
                    foreach ($forbiddenUses as $forbiddenUse) {
                        if (strpos($useFullName, $forbiddenUse) !== false) {
                            $couplingViolations[$classFullName][] = $useFullName;
                            break;
                        }
                    }
                }
            }
        }

        return new UseViolations($couplingViolations);
    }
}