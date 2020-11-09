<?php

/**
 * TechDivision\Import\Product\Variant\Observers\CleanUpVariantProductRelationObserver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-media
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Observers;

use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Product\Link\Utils\ProductTypes;
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface;
use TechDivision\Import\Product\Variant\Utils\ColumnKeys;
use TechDivision\Import\Product\Variant\Utils\ConfigurationKeys;
use TechDivision\Import\Product\Variant\Utils\MemberNames;

/**
 * Observer that cleaned up a product's media gallery information.
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-media
 * @link      http://www.techdivision.com
 */
class CleanUpVariantProductRelationObserver extends AbstractProductImportObserver
{

    /**
     * The product media processor instance.
     *
     * @var \TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface
     */
    protected $productVariantProcessor;

    /**
     * @var ProductBunchProcessorInterface
     */
    private $productBunchProcessor;

    /**
     * Initialize the observer with the passed product media processor instance.
     *
     * @param \TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface $productVariantProcessor The product media processor instance
     * @param ProductBunchProcessorInterface                                                 $productBunchProcessor   The product bunch processor instance
     * @param StateDetectorInterface|null                                                    $stateDetector           The state detector instance to use
     */
    public function __construct(
        ProductVariantProcessorInterface $productVariantProcessor,
        ProductBunchProcessorInterface $productBunchProcessor,
        StateDetectorInterface $stateDetector = null
    ) {
        parent::__construct($stateDetector);
        $this->productVariantProcessor = $productVariantProcessor;
        $this->productBunchProcessor = $productBunchProcessor;
        $this->stateDetector = $stateDetector;
    }

    /**
     * Return's the product media processor instance.
     *
     * @return \TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface The product media
     *     processor instance
     */
    protected function getProductVariantProcessor()
    {
        return $this->productVariantProcessor;
    }

    /**
     * Return's the product bunch processor instance.
     *
     * @return \TechDivision\Import\Product\Services\ProductBunchProcessorInterface The product bunch processor instance
     */
    public function getProductBunchProcessor()
    {
        return $this->productBunchProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     * @throws \Exception
     */
    protected function process()
    {
        // query whether or not we've found a configurable product
        if ($this->getValue(ColumnKeys::PRODUCT_TYPE) !== ProductTypes::CONFIGURABLE) {
            return;
        }

        // query whether or not the media gallery has to be cleaned up
        if (($this->getSubject()->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_VARIANTS)
            && $this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::CLEAN_UP_VARIANTS))
        ) {
            $this->findOldVariantsAndCleanUp();

            // log a message that the images has been cleaned-up
            $this->getSubject()
                ->getSystemLogger()
                ->debug(
                    $this->getSubject()->appendExceptionSuffix(
                        sprintf(
                            'Successfully clean up variants for product with SKU "%s"',
                            $this->getValue(ColumnKeys::SKU)
                        )
                    )
                );
        }
    }

    /**
     * Search for variants in the artefact in check differenz to database.
     * Remove entries in DB that not exist in artefact
     *
     * @return void
     * @throws \Exception
     */
    protected function findOldVariantsAndCleanUp()
    {
        $artefacts = $this->getSubject()->getArtefacts();
        if (!isset($artefacts[ProductVariantObserver::ARTEFACT_TYPE])) {
            return;
        }

        $parentIdForArtefacts = $this->getLastEntityId();
        if (!isset($artefacts[ProductVariantObserver::ARTEFACT_TYPE][$parentIdForArtefacts])) {
            return;
        }

        $actualVariants = [];
        $actualAttributes = [];
        $allVariants = $artefacts[ProductVariantObserver::ARTEFACT_TYPE][$parentIdForArtefacts];
        foreach ($allVariants as $variantData) {
            $actualVariants[] = $variantData[ColumnKeys::VARIANT_CHILD_SKU];
            $actualAttributes[$variantData[ColumnKeys::VARIANT_ATTRIBUTE_CODE]] =
                $variantData[ColumnKeys::VARIANT_ATTRIBUTE_CODE];
        }

        $parentId = $this->getLastPrimaryKey();
        // delete not exists import variants from database
        $this->cleanUpVariantChildren($parentId, $actualVariants);
        $this->cleanUpVariantAttributes($parentId, $actualAttributes);
    }

    /**
     * Delete not exists import variants from database
     *
     * @param int   $parentProductId parent product ID
     * @param array $childData       array of variants
     * @return void
     * @throws \Exception
     */
    protected function cleanUpVariantChildren($parentProductId, array $childData)
    {
        // we don't want delete everything
        if (empty($childData)) {
            return;
        }
        $parentSku = $this->getValue(ColumnKeys::SKU);
        try {
            // remove the old variantes from the database
            $this->getProductVariantProcessor()
                ->deleteProductSuperLink(
                    array(
                        MemberNames::PARENT_ID => $parentProductId,
                        MemberNames::SKU => $childData
                    )
                );

            // log a debug message that the image has been removed
            $this->getSubject()
                ->getSystemLogger()
                ->debug(
                    $this->getSubject()->appendExceptionSuffix(
                        sprintf(
                            'Successfully clean up variants for product with SKU "%s" except "%s"',
                            $parentSku,
                            implode(', ', $childData)
                        )
                    )
                );
        } catch (\Exception $e) {
            // log a warning if debug mode has been enabled and the file is NOT available
            if ($this->getSubject()->isDebugMode()) {
                $this->getSubject()
                    ->getSystemLogger()
                    ->critical($this->getSubject()->appendExceptionSuffix($e->getMessage()));
            } else {
                throw $e;
            }
        }
    }

    /**
     * Delete not exists import variants from database
     *
     * @param int   $parentProductId  parent product ID
     * @param array $actualAttributes array of actual attributes
     * @return void
     * @throws \Exception
     */
    protected function cleanUpVariantAttributes($parentProductId, array $actualAttributes)
    {
        $parentSku = $this->getValue(ColumnKeys::SKU);
        $allProductAttributes = $this->getSubject()->getAttributes();
        // search and collect attribute ID from $attributeCode
        $attributeIdFromParentProduct = [];
        foreach ($actualAttributes as $attributeCode) {
            if (isset($allProductAttributes[$attributeCode])) {
                $attributeIdFromParentProduct[] = $allProductAttributes[$attributeCode][MemberNames::ATTRIBUTE_ID];
            }
        }

        // we don't want delete everything
        if (empty($attributeIdFromParentProduct)) {
            return;
        }

        try {
            // remove the old super attributes from the database
            $this->getProductVariantProcessor()
                ->deleteProductSuperAttribute(
                    array(
                        MemberNames::PRODUCT_ID => $parentProductId,
                        MemberNames::ATTRIBUTE_ID => $attributeIdFromParentProduct
                    )
                );

            // log a debug message that the image has been removed
            $this->getSubject()
                ->getSystemLogger()
                ->info(
                    $this->getSubject()->appendExceptionSuffix(
                        sprintf(
                            'Successfully clean up attributes for product with SKU "%s"',
                            $parentSku
                        )
                    )
                );
        } catch (\Exception $e) {
            // log a warning if debug mode has been enabled and the file is NOT available
            if ($this->getSubject()->isDebugMode()) {
                $this->getSubject()
                    ->getSystemLogger()
                    ->critical($this->getSubject()->appendExceptionSuffix($e->getMessage()));
            } else {
                throw $e;
            }
        }
    }

    /**
     * Return's the PK to create the product => variant relation.
     *
     * @return integer The PK to create the relation with
     */
    protected function getLastPrimaryKey()
    {
        return $this->getLastEntityId();
    }
}
