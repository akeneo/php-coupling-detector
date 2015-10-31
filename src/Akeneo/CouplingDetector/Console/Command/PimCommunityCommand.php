<?php

namespace Akeneo\CouplingDetector\Console\Command;

use Akeneo\CouplingDetector\Coupling\UseViolations;
use Akeneo\CouplingDetector\Detector;
use Akeneo\CouplingDetector\Coupling\UseViolationsFilter;
use Akeneo\CouplingDetector\FilesReader;
use Akeneo\CouplingDetector\RulesApplier;
use Akeneo\CouplingDetector\UseViolationRule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Detects the coupling issues in pim-community-dev
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class PimCommunityCommand extends Command
{
    /**
     * {@inheritedDoc}
     */
    protected function configure()
    {
        $this
            ->setName('pim-community-dev')
            ->setDefinition(
                array(
                    new InputOption(
                        'output',
                        '',
                        InputOption::VALUE_REQUIRED, 'Output mode, "default", "count", "none"', 'default'
                    ),
                    new InputOption('strict', '', InputOption::VALUE_NONE, 'Apply strict rules without legacy exceptions'),
                )
            )
            ->setDescription('Detect coupling violations in pim-community-dev');
    }

    /**
     * {@inheritedDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->getProjectPath();
        $strictMode = $input->getOption('strict');
        $displayMode = $input->getOption('output');

        if ('none' !== $displayMode) {
            $output->writeln(
                sprintf(
                    '<info> Detect coupling violations (strict mode %s)</info>',
                    $strictMode ? 'enabled' : 'disabled'
                )
            );
        }

        $rules = [
            new UseViolationRule(
                'Akeneo\Component',
                ['Pim', 'PimEnterprise', 'Bundle', 'Doctrine\ORM']
            ),
            new UseViolationRule(
                'Akeneo\Bundle',
                ['Pim', 'PimEnterprise']
            ),
            new UseViolationRule(
                'Pim\Component',
                ['PimEnterprise', 'Bundle', 'Doctrine\ORM']
            ),
            new UseViolationRule(
                'Pim\Bundle',
                ['PimEnterprise']
            ),
            new UseViolationRule(
                'Pim\Bundle\CatalogBundle',
                ['EnrichBundle', 'UIBundle', 'TransformBundle', 'BaseConnectorBundle', 'ConnectorBundle', 'BatchBundle']
            ),
        ];

        $legacyExclusions = [
            // TranslatableInterface should be moved in a Akeneo component
            'Akeneo\Component\Classification\Updater\CategoryUpdater' => [
                'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface'
            ],
            // Repository interfaces should never expose QueryBuilder as parameter
            'Akeneo\Component\Classification\Repository' => [
                'Doctrine\ORM\QueryBuilder'
            ],
            'Pim\Component\Connector' => [
                // Interfaces of BatchBundle should be extracted in an Akeneo component
                'Akeneo\Bundle\BatchBundle\Entity\StepExecution',
                'Akeneo\Bundle\BatchBundle\Entity\JobExecution',
                'Akeneo\Bundle\BatchBundle\Item\InvalidItemException',
                'Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface',
                'Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface',
                'Akeneo\Bundle\BatchBundle\Item\UploadedFileAwareInterface',
                'Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement',
                'Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface',
                'Akeneo\Bundle\BatchBundle\Job\RuntimeErrorException',
                'Akeneo\Bundle\BatchBundle\Step\AbstractStep',
                'Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface',
                // Model interfaces of CatalogBundle should be extracted in the catalog component
                'Pim\Bundle\CatalogBundle\Model\ProductInterface',
                'Pim\Bundle\CatalogBundle\Model\AttributeInterface',
                'Pim\Bundle\CatalogBundle\Model\AssociationTypeInterface',
                'Pim\Bundle\CatalogBundle\Model\FamilyInterface',
                'Pim\Bundle\CatalogBundle\Model\GroupInterface',
                'Pim\Bundle\CatalogBundle\Model\AttributeOptionInterface',
                'Pim\Bundle\CatalogBundle\Model\ProductValueInterface',
                // Repositories interfaces of CatalogBundle should be extracted in the catalog component
                'Pim\Bundle\CatalogBundle\Repository\AttributeRepositoryInterface',
                'Pim\Bundle\CatalogBundle\Repository\LocaleRepositoryInterface',
                'Pim\Bundle\CatalogBundle\Repository\CurrencyRepositoryInterface',
                'Pim\Bundle\CatalogBundle\Repository\GroupTypeRepositoryInterface',
                'Pim\Bundle\CatalogBundle\Repository\AssociationTypeRepositoryInterface',
                'Pim\Bundle\CatalogBundle\Repository\ChannelRepositoryInterface',
                // AttributeTypes of CatalogBundle should be extracted in the catalog component
                'Pim\Bundle\CatalogBundle\AttributeType\AttributeTypes',
                'Pim\Bundle\CatalogBundle\AttributeType\AbstractAttributeType',
                // We need to check why we use these classes, interfaces should be extracted in the catalog component
                'Pim\Bundle\CatalogBundle\Manager\AttributeValuesResolver',
                'Pim\Bundle\CatalogBundle\Manager\ProductTemplateApplierInterface',
                'Pim\Bundle\CatalogBundle\Validator\Constraints\File',
                // For factories and builders of CatalogBundle, interfaces should be created in the catalog component
                'Pim\Bundle\CatalogBundle\Builder\ProductBuilderInterface',
                'Pim\Bundle\CatalogBundle\Factory\AttributeFactory',
                'Pim\Bundle\CatalogBundle\Factory\AssociationTypeFactory',
                'Pim\Bundle\CatalogBundle\Factory\FamilyFactory',
                'Pim\Bundle\CatalogBundle\Factory\GroupFactory',
                // Version manager should be exploded with SRP and introduce different interfaces in a component
                'Pim\Bundle\VersioningBundle\Manager\VersionManager'
            ],
            // Connector component should not rely on base connector file writer, move the implementation in BC manner
            'Pim\Component\Connector\Writer\File\YamlWriter' => [
                'Pim\Bundle\BaseConnectorBundle\Writer\File\FileWriter'
            ],
            'Pim\Bundle\CatalogBundle\Repository' => [
                // CatalogBundle repository interfaces should not rely on an EnrichBundle DataTransformer interface,
                // this enrich interface is not even related to UI and should be moved
                'Pim\Bundle\EnrichBundle\Form\DataTransformer\ChoicesProviderInterface',
                // CatalogBundle repository interfaces should not rely on a UIBundle repository interface, this ui
                // interface should be moved
                'Pim\Bundle\UIBundle\Entity\Repository\OptionRepositoryInterface'
            ],
            // CatalogBundle MongoDB normalizers should not use a TransformBundle normalizer, will be better to
            // duplicate code or extract
            'Pim\Bundle\CatalogBundle\MongoDB\Normalizer' => [
                'Pim\Bundle\TransformBundle\Normalizer\Structured\TranslationNormalizer'
            ]
        ];
        $useViolationsFilter = new UseViolationsFilter($legacyExclusions);

        $detector = new Detector();
        $reader = new FilesReader($path);
        $applier = new RulesApplier($rules);

        $violations = $detector->detectUseViolations($reader, $applier);
        if (!$strictMode) {
            $violations = $useViolationsFilter->filter($violations);
        }

        if ('default' === $displayMode) {
            $this->displayStandardViolations($output, $violations);

        } elseif ('count' === $displayMode) {
            $this->displayCounterViolations($output, $violations);
        }

        return count($violations->getFullQualifiedClassNameViolations()) > 0;
    }

    /**
     * @param OutputInterface $output
     * @param UseViolations   $violations
     */
    protected function displayStandardViolations(OutputInterface $output, UseViolations $violations)
    {
        $violations = $violations->getFullQualifiedClassNameViolations();
        $totalCount = 0;
        foreach ($violations as $className => $violationUses) {
            if (0 < count($violationUses)) {
                $output->writeln(sprintf('<info>%s</info>', $className));
            }
            foreach ($violationUses as $use) {
                $output->writeln(sprintf('<info> - use %s</info>', $use));
            }
            $totalCount += count($violationUses);
        }
        $output->writeln(sprintf('<info>Total coupling issues %s</info>', $totalCount));
    }

    /**
     * @param OutputInterface $output
     * @param UseViolations   $violations
     */
    protected function displayCounterViolations(OutputInterface $output, UseViolations $violations)
    {
        $forbiddenUseCounter = $violations->getSortedForbiddenUsesCounters();
        $totalCount = 0;
        foreach ($forbiddenUseCounter as $fullName => $count) {
            $output->writeln(sprintf('<info> - %d x %s</info>', $count, $fullName));
            $totalCount += $count;
        }
        $output->writeln(sprintf('<info>Total coupling issues %s</info>', $totalCount));
    }

    /**
     * @return string
     */
    protected function getProjectPath()
    {
        $binPath = getcwd();
        $composerPath = $binPath.'/composer.json';
        $errorMessage = sprintf(
                'You must launch the command from the pim-community-dev repository, not from "%s"',
                $binPath
            ).PHP_EOL;
        if (!file_exists($composerPath)) {
            fwrite(STDERR, $errorMessage);
            exit(1);
        } else {
            $composerContent = file_get_contents($composerPath);
            if (false === strpos($composerContent, '"name": "akeneo/pim-community-dev"')) {
                fwrite(STDERR, $errorMessage);
                exit(1);
            }
        }

        return $binPath.'/src/';
    }
}
