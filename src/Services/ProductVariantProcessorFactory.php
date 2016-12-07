<?php

/**
 * TechDivision\Import\Product\Variant\Services\ProductVariantProcessorFactory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Product\Variant\Services;

use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Services\AbstractProcessorFactory;
use TechDivision\Import\Repositories\EavAttributeRepository;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepository;
use TechDivision\Import\Product\Variant\Actions\ProductRelationAction;
use TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeAction;
use TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeLabelAction;
use TechDivision\Import\Product\Variant\Actions\ProductSuperLinkAction;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductRelationPersistProcessor;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributePersistProcessor;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeLabelPersistProcessor;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperLinkPersistProcessor;

/**
 * A SLSB providing methods to load product data using a PDO connection.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ProductVariantProcessorFactory extends AbstractProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Product\Variant\Services\ProductVariantProcessor';
    }

    /**
     * Factory method to create a new product variant processor instance.
     *
     * @param \PDO                                       $connection    The PDO connection to use
     * @param TechDivision\Import\ConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Variant\Services\ProductVariantProcessor The processor instance
     */
    public function factory(\PDO $connection, ConfigurationInterface $configuration)
    {

        // extract Magento edition/version
        $magentoEdition = $configuration->getMagentoEdition();
        $magentoVersion = $configuration->getMagentoVersion();

        // initialize the repository that provides EAV attribute query functionality
        $eavAttributeRepository = new EavAttributeRepository();
        $eavAttributeRepository->setMagentoEdition($magentoEdition);
        $eavAttributeRepository->setMagentoVersion($magentoVersion);
        $eavAttributeRepository->setConnection($connection);
        $eavAttributeRepository->init();

        // initialize the repository that provides EAV attribute option value query functionality
        $eavAttributeOptionValueRepository = new EavAttributeOptionValueRepository();
        $eavAttributeOptionValueRepository->setMagentoEdition($magentoEdition);
        $eavAttributeOptionValueRepository->setMagentoVersion($magentoVersion);
        $eavAttributeOptionValueRepository->setConnection($connection);
        $eavAttributeOptionValueRepository->init();

        // initialize the action that provides product relation CRUD functionality
        $productRelationPersistProcessor = new ProductRelationPersistProcessor();
        $productRelationPersistProcessor->setMagentoEdition($magentoEdition);
        $productRelationPersistProcessor->setMagentoVersion($magentoVersion);
        $productRelationPersistProcessor->setConnection($connection);
        $productRelationPersistProcessor->init();
        $productRelationAction = new ProductRelationAction();
        $productRelationAction->setPersistProcessor($productRelationPersistProcessor);

        // initialize the action that provides product super attribute CRUD functionality
        $productSuperAttributePersistProcessor = new ProductSuperAttributePersistProcessor();
        $productSuperAttributePersistProcessor->setMagentoEdition($magentoEdition);
        $productSuperAttributePersistProcessor->setMagentoVersion($magentoVersion);
        $productSuperAttributePersistProcessor->setConnection($connection);
        $productSuperAttributePersistProcessor->init();
        $productSuperAttributeAction = new ProductSuperAttributeAction();
        $productSuperAttributeAction->setPersistProcessor($productSuperAttributePersistProcessor);

        // initialize the action that provides product super attribute label CRUD functionality
        $productSuperAttributeLabelPersistProcessor = new ProductSuperAttributeLabelPersistProcessor();
        $productSuperAttributeLabelPersistProcessor->setMagentoEdition($magentoEdition);
        $productSuperAttributeLabelPersistProcessor->setMagentoVersion($magentoVersion);
        $productSuperAttributeLabelPersistProcessor->setConnection($connection);
        $productSuperAttributeLabelPersistProcessor->init();
        $productSuperAttributeLabelAction = new ProductSuperAttributeLabelAction();
        $productSuperAttributeLabelAction->setPersistProcessor($productSuperAttributeLabelPersistProcessor);

        // initialize the action that provides product super link CRUD functionality
        $productSuperLinkPersistProcessor = new ProductSuperLinkPersistProcessor();
        $productSuperLinkPersistProcessor->setMagentoEdition($magentoEdition);
        $productSuperLinkPersistProcessor->setMagentoVersion($magentoVersion);
        $productSuperLinkPersistProcessor->setConnection($connection);
        $productSuperLinkPersistProcessor->init();
        $productSuperLinkAction = new ProductSuperLinkAction();
        $productSuperLinkAction->setPersistProcessor($productSuperLinkPersistProcessor);

        // initialize the product variant processor
        $processorType = ProductVariantProcessorFactory::getProcessorType();
        $productVariantProcessor = new $processorType();
        $productVariantProcessor->setConnection($connection);
        $productVariantProcessor->setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository);
        $productVariantProcessor->setEavAttributeRepository($eavAttributeRepository);
        $productVariantProcessor->setProductRelationAction($productRelationAction);
        $productVariantProcessor->setProductSuperLinkAction($productSuperLinkAction);
        $productVariantProcessor->setProductSuperAttributeAction($productSuperAttributeAction);
        $productVariantProcessor->setProductSuperAttributeLabelAction($productSuperAttributeLabelAction);

        // return the instance
        return $productVariantProcessor;
    }
}
