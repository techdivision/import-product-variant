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
     * Return's the primary key of the product to load.
     *
     * @param array $product product array like from ProductBunchProcessorInterface::loadProduct
     * @return integer The primary key of the product
     */
    protected function getPrimaryKey(array $product)
    {
        return isset($product[\TechDivision\Import\Product\Utils\MemberNames::ENTITY_ID])
            ? $product[\TechDivision\Import\Product\Utils\MemberNames::ENTITY_ID] : null;
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
        if (isset($artefacts[ProductVariantObserver::ARTEFACT_TYPE]) === null) {
            return;
        }

        $actualVariants = [];
        $actualAttributes = [];
        foreach ($artefacts[ProductVariantObserver::ARTEFACT_TYPE] as $allVariants) {
            foreach ($allVariants as $variantData) {
                $parentId = null;
                $childId = null;
                try {
                    // try to load and map the parent ID
                    $product = $this->getProductBunchProcessor()
                        ->loadProduct($variantData[ColumnKeys::VARIANT_PARENT_SKU]);
                    $parentId = $this->getPrimaryKey($product);
                } catch (\Exception $e) {
                    throw $this->wrapException(array(ColumnKeys::VARIANT_PARENT_SKU), $e);
                }

                try {
                    // try to load and map the child ID
                    $product =
                        $this->getProductBunchProcessor()->loadProduct($variantData[ColumnKeys::VARIANT_CHILD_SKU]);
                    $childId = $this->getPrimaryKey($product);
                } catch (\Exception $e) {
                    throw $this->wrapException(array(ColumnKeys::VARIANT_CHILD_SKU), $e);
                }
                if ($parentId && $childId) {
                    $actualVariants[$parentId][$childId] = $childId;
                    $actualAttributes[$parentId][$variantData[ColumnKeys::VARIANT_ATTRIBUTE_CODE]] =
                        $variantData[ColumnKeys::VARIANT_ATTRIBUTE_CODE];
                }
            }
        }

        // delete not exists import variants from database
        $this->cleanUpVariantChildren($actualVariants);
        $this->cleanUpVariantAttributes($actualAttributes);
    }

    /**
     * Delete not exists import variants from database
     *
     * @param array $actualVariants array of variants
     * @return void
     * @throws \Exception
     */
    protected function cleanUpVariantChildren(array $actualVariants)
    {
        $parentSku = $this->getValue(ColumnKeys::SKU);
        foreach ($actualVariants as $parentProductId => $childData) {
            // load the existing variant entities for the product with the given SKU
            foreach ($this->getProductVariantProcessor()
                         ->loadProductSuperLinksFromParent($parentProductId) as $existingVariantChildren) {
                if (in_array($existingVariantChildren[MemberNames::PRODUCT_ID], $childData)) {
                    continue;
                }

                try {
                    // remove the old variantes from the database
                    $this->getProductVariantProcessor()
                        ->deleteProductSuperLink(array(MemberNames::LINK_ID => $existingVariantChildren[MemberNames::LINK_ID]));

                    // log a debug message that the image has been removed
                    $this->getSubject()
                        ->getSystemLogger()
                        ->info(
                            $this->getSubject()->appendExceptionSuffix(
                                sprintf(
                                    'Successfully clean up variants for product with SKU "%s"',
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
        }
    }

    /**
     * Delete not exists import variants from database
     *
     * @param array $actualAttributes array of actual attributes
     * @return void
     * @throws \Exception
     */
    protected function cleanUpVariantAttributes(array $actualAttributes)
    {
        $parentSku = $this->getValue(ColumnKeys::SKU);
        $allProductAttributes = $this->getSubject()->getAttributes();
        foreach ($actualAttributes as $parentProductId => $attributeCodes) {
            // search and collect attribute ID from $attributeCode
            $attributeFromParentProduct = [];
            foreach ($attributeCodes as $attributeCode) {
                if (isset($allProductAttributes[$attributeCode])) {
                    $attributeFromParentProduct[] = $allProductAttributes[$attributeCode][MemberNames::ATTRIBUTE_ID];
                }
            }
            // load the existing super attributes for the product with the given SKU
            foreach ($this->getProductVariantProcessor()
                         ->loadProductSuperAttributesFromProduct($parentProductId) as $existingSuperAttribute) {
                if (in_array($existingSuperAttribute[MemberNames::ATTRIBUTE_ID], $attributeFromParentProduct)) {
                    continue;
                }

                try {
                    // remove the old super attributes from the database
                    $this->getProductVariantProcessor()
                        ->deleteProductSuperAttribute(array(MemberNames::PRODUCT_SUPER_ATTRIBUTE_ID => $existingSuperAttribute[MemberNames::PRODUCT_SUPER_ATTRIBUTE_ID]));

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
        }
    }
}
