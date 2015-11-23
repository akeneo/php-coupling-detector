<?php

namespace Akeneo\CouplingDetector\Console\Command;

use Akeneo\CouplingDetector\Coupling\UseViolations;
use Akeneo\CouplingDetector\CouplingDetector;
use Akeneo\CouplingDetector\Data\Rule;
use Akeneo\CouplingDetector\Data\RuleInterface;
use Akeneo\CouplingDetector\Data\ViolationInterface;
use Akeneo\CouplingDetector\FilesReader;
use Akeneo\CouplingDetector\NodeExtractor\NodeExtractorResolver;
use Akeneo\CouplingDetector\RuleChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

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
                        InputOption::VALUE_REQUIRED, 'Output mode, "default", "none"', 'default'
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

        $finder = new Finder();
        $finder
            ->files()
            ->in($path)
            ->name('*.php')
            ->notPath('Oro')
        ;

        $nodeExtractorResolver = new NodeExtractorResolver();
        $ruleChecker = new RuleChecker();
        $coupling = new CouplingDetector($nodeExtractorResolver, $ruleChecker);
        $violations = $coupling->detect($finder, $this->getRules());

        if ('none' !== $displayMode) {
            $this->displayStandardViolations($output, $violations);
        }

        return count($violations) > 0 ? 1 : 0;
    }

    /**
     * @param OutputInterface       $output
     * @param ViolationInterface[]  $violations
     */
    protected function displayStandardViolations(OutputInterface $output, array $violations)
    {
        $totalCount = 0;
        foreach ($violations as $violation) {
            $rule = $violation->getRule();
            $node = $violation->getNode();
            $errorType = RuleInterface::TYPE_DISCOURAGED === $rule->getType() ? 'warning' : 'error';

            $output->writeln('');
            $output->writeln(sprintf('Rule "%s" violated in file "%s"', $rule->getSubject(), $node->getFilepath()));
            foreach ($violation->getTokenViolations() as $token) {
                $output->writeln(sprintf('<%s> - use %s</%s>', $errorType, $token, $errorType));
            }
            $totalCount += count($violation->getTokenViolations());
        }
        $output->writeln(sprintf('<info>Total coupling issues: %d.</info>', $totalCount));
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

    /**
     * @return array
     */
    private function getRules()
    {
        $rules = [
            new Rule(
                'Akeneo\Component',
                ['Pim', 'PimEnterprise', 'Bundle', 'Doctrine\ORM'],
                RuleInterface::TYPE_FORBIDDEN
            ),
            new Rule(
                'Akeneo\Bundle',
                ['Pim', 'PimEnterprise'],
                RuleInterface::TYPE_FORBIDDEN
            ),
            new Rule(
                'Pim\Component',
                ['PimEnterprise', 'Bundle', 'Doctrine\ORM'],
                RuleInterface::TYPE_FORBIDDEN
            ),
            new Rule(
                'Pim\Bundle',
                ['PimEnterprise'],
                RuleInterface::TYPE_FORBIDDEN
            ),
            new Rule(
                'Pim\Bundle\CatalogBundle',
                [
                    // bundles
                    'AnalyticsBundle',
                    'CommentBundle',
                    'DataGridBundle',
                    'ImportExportBundle',
                    'LocalizationBundle',
                    'PdfGeneratorBundle',
                    'TranslationBundle',
                    'VersioningBundle',
                    'BaseConnectorBundle',
                    'ConnectorBundle',
                    'EnrichBundle',
                    'InstallerBundle',
                    'NavigationBundle',
                    'ReferenceDataBundle',
                    'UIBundle',
                    'WebServiceBundle',
                    'DashboardBundle',
                    'FilterBundle',
                    'JsFormValidationBundle',
                    'NotificationBundle',
                    'TransformBundle',
                    'UserBundle',
                    'BatchBundle',
                    // components
                    'Connector'
                ],
                RuleInterface::TYPE_FORBIDDEN
            ),
            new Rule(
                'Pim\Bundle\ConnectorBundle',
                [
                    'AnalyticsBundle',
                    'CommentBundle',
                    'DataGridBundle',
                    'ImportExportBundle',
                    'LocalizationBundle',
                    'PdfGeneratorBundle',
                    'TranslationBundle',
                    'VersioningBundle',
                    'BaseConnectorBundle',
                    'CatalogBundle',
                    'EnrichBundle',
                    'InstallerBundle',
                    'NavigationBundle',
                    'ReferenceDataBundle',
                    'UIBundle',
                    'WebServiceBundle',
                    'DashboardBundle',
                    'FilterBundle',
                    'JsFormValidationBundle',
                    'NotificationBundle',
                    'TransformBundle',
                    'UserBundle'
                ],
                RuleInterface::TYPE_FORBIDDEN
            ),
        ];

        return $rules;
    }

    /**
     * @return array
     */
    private function getLegacyExclusions()
    {
        $legacyExclusions = [
            // TranslatableInterface should be moved in a Akeneo component
            'Akeneo\Component\Classification\Updater\CategoryUpdater'   => [
                'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface'
            ],
            // Repository interfaces should never expose QueryBuilder as parameter
            'Akeneo\Component\Classification\Repository'                => [
                'Doctrine\ORM\QueryBuilder'
            ],
            'Pim\Component\Catalog'                                     => [
                // Model interfaces of CatalogBundle should be extracted in the catalog component
                'Pim\Bundle\CatalogBundle\Model\ChannelInterface',
                'Pim\Bundle\CatalogBundle\Model\LocaleInterface',
                'Pim\Bundle\CatalogBundle\Model\ProductValueInterface',
                'Pim\Bundle\CatalogBundle\Model\AttributeInterface',
                'Pim\Bundle\CatalogBundle\Model\ProductInterface',
                'Pim\Bundle\CatalogBundle\Model\AttributeOptionInterface',
                'Pim\Bundle\CatalogBundle\Model\AssociationInterface',
                'Pim\Bundle\CatalogBundle\Model\CategoryInterface',
                'Pim\Bundle\CatalogBundle\Model\FamilyInterface',
                'Pim\Bundle\CatalogBundle\Model\AttributeGroupInterface',
                'Pim\Bundle\CatalogBundle\Model\GroupInterface',
                'Pim\Bundle\CatalogBundle\Model\AssociationTypeInterface',
                'Pim\Bundle\CatalogBundle\Model\ProductTemplateInterface',
                'Pim\Bundle\CatalogBundle\Model\AttributeRequirementInterface',
                // Repository interfaces of CatalogBundle should be extracted in the catalog component
                'Pim\Bundle\CatalogBundle\Repository\AttributeRepositoryInterface',
                'Pim\Bundle\CatalogBundle\Repository\ChannelRepositoryInterface',
                'Pim\Bundle\CatalogBundle\Repository\GroupTypeRepositoryInterface',
                'Pim\Bundle\CatalogBundle\Repository\LocaleRepositoryInterface',
                'Pim\Bundle\CatalogBundle\Repository\AttributeGroupRepositoryInterface',
                'Pim\Bundle\CatalogBundle\Repository\AttributeRequirementRepositoryInterface',
                // Builder interface should be extracted in the catalog component
                'Pim\Bundle\CatalogBundle\Builder\ProductBuilderInterface',
                // Extract at least an interface of these factories in the catalog component (ideally move implem too)
                'Pim\Bundle\CatalogBundle\Factory\FamilyFactory',
                'Pim\Bundle\CatalogBundle\Factory\AttributeRequirementFactory',
                'Pim\Bundle\CatalogBundle\Factory\MetricFactory',
                // What to do with this class?
                'Pim\Bundle\CatalogBundle\Validator\AttributeValidatorHelper',
                // Extract this exception in the component, it should be more accurate than InvalidArgumentException
                // as it looks only used in updaters
                'Pim\Bundle\CatalogBundle\Exception\InvalidArgumentException',
                // Avoid to use this manager, extract an interface from this or maybe use repository and depreciate it
                'Pim\Bundle\CatalogBundle\Manager\CurrencyManager',
                // What to do with this, cannot be extracted due to dependencies to symfony form
                'Pim\Bundle\CatalogBundle\AttributeType\AbstractAttributeType',
                'Pim\Bundle\CatalogBundle\AttributeType\AttributeTypes',
                // Deprecated in 1.5, should be dropped the deprecated methods support
                'Pim\Bundle\CatalogBundle\Updater\ProductUpdaterInterface'
            ],
            'Pim\Component\Connector'                                   => [
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
                // What to do with this, cannot be extracted due to dependencies to symfony form
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
            'Pim\Component\Connector\Writer\File\YamlWriter'            => [
                'Pim\Bundle\BaseConnectorBundle\Writer\File\FileWriter'
            ],
            // Same issues than catalog component updater classes, same fixes expected
            'Pim\Component\ReferenceData\Updater'                       => [
                'Pim\Bundle\CatalogBundle\Builder\ProductBuilderInterface',
                'Pim\Bundle\CatalogBundle\Model\AttributeInterface',
                'Pim\Bundle\CatalogBundle\Model\ProductInterface',
                'Pim\Bundle\CatalogBundle\Model\ProductValueInterface',
                'Pim\Bundle\CatalogBundle\Validator\AttributeValidatorHelper',
                'Pim\Bundle\CatalogBundle\Exception\InvalidArgumentException',
            ],
            // Same issues than catalog component updater classes, same fixes expected
            'Pim\Component\Localization'                                => [
                'Pim\Bundle\CatalogBundle\Model\MetricInterface',
                'Pim\Bundle\CatalogBundle\Model\ProductPriceInterface',
                'Pim\Bundle\CatalogBundle\Model\ProductValueInterface',
                'Pim\Bundle\CatalogBundle\AttributeType\AttributeTypes',
                'Pim\Bundle\CatalogBundle\Repository\AttributeRepositoryInterface',
                // Why we use it?
                'Pim\Component\Localization\Normalizer\MetricNormalizer',
            ],
            'Pim\Bundle\CatalogBundle\Model'                            => [
                // should be extracted in a component in a akeneo component in a BC way (localization?)
                'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface',
                'Pim\Bundle\TranslationBundle\Entity\AbstractTranslation',
                // should be extracted in a akeneo component in a BC way
                'Pim\Bundle\VersioningBundle\Model\VersionableInterface',
                // should be extracted in a akeneo component in a BC way
                'Pim\Bundle\CommentBundle\Model\CommentSubjectInterface'
            ],
            'Pim\Bundle\CatalogBundle\Entity'                           => [
                // should be extracted in a component in a akeneo component in a BC way (localization?)
                'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface',
                'Pim\Bundle\TranslationBundle\Entity\AbstractTranslation',
                // should be extracted in a akeneo component in a BC way
                'Pim\Bundle\VersioningBundle\Model\VersionableInterface',
            ],
            'Pim\Bundle\CatalogBundle\EventSubscriber'                  => [
                // should be extracted in a akeneo component in a BC way
                'Pim\Bundle\VersioningBundle\Model\VersionableInterface',
            ],
            'Pim\Bundle\CatalogBundle\Doctrine\Common\Saver\GroupSaver' => [
                // what to do with this, it's a weird way to share and update versionning context, could be re-worked
                // with the versioning reworking (no more relying on doctrine events)
                'Pim\Bundle\VersioningBundle\Manager\VersionContext'
            ],
            'Pim\Bundle\CatalogBundle\Manager\FamilyManager'            => [
                // FamilyManager should be dropped and not even used
                'Pim\Bundle\UserBundle\Context\UserContext'
            ],
            'Pim\Bundle\CatalogBundle\Helper\LocaleHelper'              => [
                // LocaleHelper should be simplified and moved to LocalizationBundle
                'Pim\Bundle\UserBundle\Context\UserContext'
            ],
            'Pim\Bundle\CatalogBundle\Repository'                       => [
                // CatalogBundle repository interfaces should not rely on an EnrichBundle DataTransformer interface,
                // this enrich interface is not even related to UI and should be moved
                'Pim\Bundle\EnrichBundle\Form\DataTransformer\ChoicesProviderInterface',
                // CatalogBundle repository interfaces should not rely on a UIBundle repository interface, this ui
                // interface should be moved
                'Pim\Bundle\UIBundle\Entity\Repository\OptionRepositoryInterface'
            ],
            // CatalogBundle MongoDB normalizers should not use a TransformBundle normalizer, will be better to
            // duplicate code or extract
            'Pim\Bundle\CatalogBundle\MongoDB\Normalizer'               => [
                'Pim\Bundle\TransformBundle\Normalizer\Structured\TranslationNormalizer'
            ],
        ];

        return $legacyExclusions;
    }
}
