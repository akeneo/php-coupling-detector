<?php

namespace Akeneo\Inspector\Console\Command;

use Akeneo\Inspector\Coupling\Detector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Detects the coupling issues in pim-community-dev
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class CouplingPimCommunityCommand extends Command
{
    /**
     * {@inheritedDoc}
     */
    protected function configure()
    {
        $this
            ->setName('coupling-pim-community-dev')
            ->setDefinition(
                array(new InputArgument('path', InputArgument::REQUIRED, 'The pim community dev src path', null))
            )
            ->setDescription('Detect coupling violations in pim-community-dev');
    }

    /**
     * {@inheritedDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        $namespaceToForbiddenUse = [
            'Akeneo/Component' => ['Pim', 'PimEnterprise', 'Bundle'],
            'Akeneo/Bundle'    => ['Pim', 'PimEnterprise'],
            'Pim/Component'    => ['PimEnterprise', 'Bundle'],
            'Pim/Bundle'       => ['PimEnterprise'],
        ];

        foreach ($namespaceToForbiddenUse as $namespace => $forbiddenUse) {
            $detector = new Detector($namespace, $forbiddenUse);
            $violation = $detector->detectCoupling($path, $namespace, $forbiddenUse);
            $forbiddenUseCounter = $violation->getSortedForbiddenUsesCounter();
            $totalCount = 0;
            $output->writeln(sprintf('<info>>> Inspect namespace %s</info>', $namespace));
            foreach ($forbiddenUseCounter as $fullName => $count) {
                $output->writeln(sprintf('<info> - %d x %s</info>', $count, $fullName));
                $totalCount += $count;
            }
            $output->writeln(sprintf('<info>Total coupling issues %d</info>', $totalCount));
        }
    }
}
