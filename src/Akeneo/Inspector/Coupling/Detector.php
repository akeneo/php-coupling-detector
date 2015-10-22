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
     * @return Violation
     */
    public function detectCoupling($path, $namespace, $forbiddenUses)
    {
        $forbiddenUseCounter = [];
        $couplingViolations = [];
        $finder = new Finder();
        $finder->files()->in($path)->name('*.php');
        foreach ($finder as $file) {
            if (strpos($file->getRelativePath(), $namespace) === 0) {

                $relativePathName = $file->getRelativePathname();
                $content = $file->getContents();
                $tokens = Tokens::fromCode($content);
                $useDeclarationsIndexes = $tokens->getImportUseIndexes();
                $useDeclarationExtractor = new NamespaceUseDeclarationsExtractor();
                $useDeclarations = $useDeclarationExtractor->extractNamespaceUseDeclarations($tokens, $useDeclarationsIndexes);

                foreach ($useDeclarations as $useDeclaration) {
                    $useFullName = $useDeclaration['fullName'];
                    foreach ($forbiddenUses as $forbiddenUse) {
                        if (strpos($useFullName, $forbiddenUse) !== false) {
                            $couplingViolations[$relativePathName][] = $useFullName;
                            if (!isset($forbiddenUseCounter[$useFullName])) {
                                $forbiddenUseCounter[$useFullName] = 1;
                            } else {
                                $forbiddenUseCounter[$useFullName]++;
                            }
                        }
                    }
                }
            }
        }

        return new Violation($forbiddenUses, $forbiddenUseCounter);
    }
}