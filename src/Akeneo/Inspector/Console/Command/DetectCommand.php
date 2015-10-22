<?php

namespace Akeneo\Inspector\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Class DetectCommand
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class DetectCommand extends Command
{
    /**
     * {@inheritedDoc}
     */
    protected function configure()
    {
        $this
            ->setName('detect-static-coupling')
            ->setDefinition(
                array(new InputArgument('path', InputArgument::OPTIONAL, 'The path', null))
            )
            ->setDescription('Detect PHP static coupling');
    }

    /**
     * {@inheritedDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        $finder = new Finder();
        $finder->files()->in($path)->name('*.php');

        $forbiddenUseCounter = [];
        $couplingViolations = [];

        foreach ($finder as $file) {
            if (strpos($file->getRelativePath(), 'Pim/Component') === 0) {

                $relativePathName = $file->getRelativePathname();
                $content = $file->getContents();
                $tokens = Tokens::fromCode($content);
                $useDeclarationsIndexes = $tokens->getImportUseIndexes();
                $useDeclarations = $this->getNamespaceUseDeclarations($tokens, $useDeclarationsIndexes);

                foreach ($useDeclarations as $useDeclaration) {
                    $useFullName = $useDeclaration['fullName'];
                    if (strpos($useFullName, 'Bundle') !== false) {
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

        /*
        if (count($couplingViolations) > 0) {
            foreach ($couplingViolations as $erroneousFile => $violations) {
                //$output->writeln(sprintf('<error>>> Coupling issues in file %s</error>', $erroneousFile));
                foreach ($violations as $violation) {
                    $output->writeln(sprintf('<error>Forbidden import use of %s</error>', $violation));
                }
            }
        }*/

        arsort($forbiddenUseCounter);
        $totalCount = 0;
        foreach ($forbiddenUseCounter as $fullName => $count) {
            $output->writeln(sprintf('<info>%d x %s</info>', $count, $fullName));
            $totalCount += $count;
        }
        $output->writeln(sprintf('<info>Total coupling issues %d</info>', $totalCount));
    }

    /**
     * Copy/paste from Symfony\CS\Fixer\Symfony\UnusedUseFixer
     *
     * @param Tokens $tokens
     * @param array $useIndexes
     *
     * @return array
     */
    private function getNamespaceUseDeclarations(Tokens $tokens, array $useIndexes)
    {
        $uses = array();

        foreach ($useIndexes as $index) {
            $declarationEndIndex = $tokens->getNextTokenOfKind($index, array(';'));
            $declarationContent = $tokens->generatePartialCode($index + 1, $declarationEndIndex - 1);

            // ignore multiple use statements like: `use BarB, BarC as C, BarD;`
            // that should be split into few separate statements
            if (false !== strpos($declarationContent, ',')) {
                continue;
            }

            $declarationParts = preg_split('/\s+as\s+/i', $declarationContent);

            if (1 === count($declarationParts)) {
                $fullName = $declarationContent;
                $declarationParts = explode('\\', $fullName);
                $shortName = end($declarationParts);
                $aliased = false;
            } else {
                $fullName = $declarationParts[0];
                $shortName = $declarationParts[1];
                $declarationParts = explode('\\', $fullName);
                $aliased = $shortName !== end($declarationParts);
            }

            $shortName = trim($shortName);

            $uses[$shortName] = array(
                'aliased' => $aliased,
                'end' => $declarationEndIndex,
                'fullName' => trim($fullName),
                'shortName' => $shortName,
                'start' => $index,
            );
        }

        return $uses;
    }


    /**
     * Get indexes of namespace uses.
     *
     * @param bool $perNamespace Return namespace uses per namespace
     *
     * @return array|array[]
     */
    public function getImportUseIndexes($perNamespace = false)
    {
        $this->rewind();

        $uses = array();
        $namespaceIndex = 0;

        for ($index = 0, $limit = $this->count(); $index < $limit; ++$index) {
            $token = $this[$index];

            if ($token->isGivenKind(T_NAMESPACE)) {
                $nextTokenIndex = $this->getNextTokenOfKind($index, array(';', '{'));
                $nextToken = $this[$nextTokenIndex];

                if ($nextToken->equals('{')) {
                    $index = $nextTokenIndex;
                }

                if ($perNamespace) {
                    ++$namespaceIndex;
                }

                continue;
            }

            // Skip whole class braces content.
            // The only { that interest us is the one directly after T_NAMESPACE and is handled above
            // That way we can skip for example whole tokens in class declaration, therefore skip `T_USE` for traits.
            if ($token->equals('{')) {
                $index = $this->findBlockEnd(self::BLOCK_TYPE_CURLY_BRACE, $index);
                continue;
            }

            if (!$token->isGivenKind(T_USE)) {
                continue;
            }

            $nextToken = $this[$this->getNextMeaningfulToken($index)];

            // ignore function () use ($foo) {}
            if ($nextToken->equals('(')) {
                continue;
            }

            $uses[$namespaceIndex][] = $index;
        }

        if (!$perNamespace && isset($uses[$namespaceIndex])) {
            return $uses[$namespaceIndex];
        }

        return $uses;
    }
}
