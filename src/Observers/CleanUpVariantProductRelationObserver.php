<?php

/**
 * TechDivision\Import\Product\Variant\Observers\CleanUpVariantProductRelationObserver
 *
 * PHP version 7
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Observers;

use TechDivision\Import\Utils\ProductTypes;
use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;
use TechDivision\Import\Product\Variant\Utils\ColumnKeys;
use TechDivision\Import\Product\Variant\Utils\MemberNames;
use TechDivision\Import\Product\Variant\Utils\ConfigurationKeys;
use TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface;

/**
 * Observer that cleaned up a product's media gallery information.
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class CleanUpVariantProductRelationObserver extends AbstractProductImportObserver
{

    /**
     * The product variant processor instance.
     *
     * @var \TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface
     */
    protected $productVariantProcessor;

    /**
     * Initialize the observer with the passed product variant processor instance.
     *
     * @param \TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface $productVariantProcessor The product variant processor instance
     * @param StateDetectorInterface|null                                                    $stateDetector           The state detector instance to use
     */
    public function __construct(
        ProductVariantProcessorInterface $productVariantProcessor,
        StateDetectorInterface $stateDetector = null
    ) {

        // pass the state detector to the parent constructor
        parent::__construct($stateDetector);

        // initialize the product variant processor instance
        $this->productVariantProcessor = $productVariantProcessor;
    }

    /**
     * Return's the product variant processor instance.
     *
     * @return \TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface The product variant processor instance
     */
    protected function getProductVariantProcessor()
    {
        return $this->productVariantProcessor;
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
        if ($this->getSubject()->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_VARIANTS) &&
            $this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::CLEAN_UP_VARIANTS)
        ) {
            // clean-up the existing variants
            $this->cleanUpVariants();

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
     * Search for variants in the artefacts and check for differences in
     * the database. Remove entries in DB that not exist in artefact.
     *
     * @return void
     * @throws \Exception Is thrown, if either the variant children und attributes can not be deleted
     */
    protected function cleanUpVariants()
    {

        // load the available artefacts from the subject
        $artefacts = $this->getSubject()->getArtefacts();

        // return, if we do NOT have any variant artefacts
        if (!isset($artefacts[ProductVariantObserver::ARTEFACT_TYPE])) {
            return;
        }

        // load the entity ID of the parent product
        $parentIdForArtefacts = $this->getLastEntityId();

        // return, if we do NOT have any artefacts for the actual entity ID
        if (!isset($artefacts[ProductVariantObserver::ARTEFACT_TYPE][$parentIdForArtefacts])) {
            return;
        }

        // initialize the array with the SKUs of
        // the child IDs and the attribute codes
        $actualVariants = [];
        $actualAttributes = [];

        // load the variant artefacts for the actual entity ID
        $allVariants = $artefacts[ProductVariantObserver::ARTEFACT_TYPE][$parentIdForArtefacts];

        // iterate over the artefacts with the variant data
        foreach ($allVariants as $variantData) {
            // add the child SKU to the array
            $actualVariants[] = $variantData[ColumnKeys::VARIANT_CHILD_SKU];
            // add the attribute code to the array
            $actualAttributes[$variantData[ColumnKeys::VARIANT_ATTRIBUTE_CODE]] =
                $variantData[ColumnKeys::VARIANT_ATTRIBUTE_CODE];
        }

        // load the row/entity ID of the parent product
        $parentId = $this->getLastPrimaryKey();

        try {
            // delete not exists import variants from database
            $this->cleanUpVariantChildren($parentId, $actualVariants);
            $this->cleanUpVariantAttributes($parentId, $actualAttributes);
        } catch (\Exception $e) {
            // log a warning if debug mode has been enabled
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
     * Delete not exists import variants from database.
     *
     * @param int   $parentProductId The ID of the parent product
     * @param array $childData       The array of variants
     *
     * @return void
     */
    protected function cleanUpVariantChildren($parentProductId, array $childData)
    {

        // we don't want delete everything
        if (empty($childData)) {
            return;
        }

        // load the SKU of the parent product
        $parentSku = $this->getValue(ColumnKeys::SKU);

        // remove the old variantes from the database
        $this->getProductVariantProcessor()
             ->deleteProductSuperLink(
                 array(
                     MemberNames::PARENT_ID => $parentProductId,
                     MemberNames::SKU       => $childData
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
    }

    /**
     * Delete not exists import variants from database.
     *
     * @param int   $parentProductId  The ID of the parent product
     * @param array $actualAttributes The array of actual attributes
     *
     * @return void
     */
    protected function cleanUpVariantAttributes($parentProductId, array $actualAttributes)
    {

        // load the SKU and the attributes from the subject
        $parentSku = $this->getValue(ColumnKeys::SKU);
        $allProductAttributes = $this->getSubject()->getAttributes();

        // search and collect attribute ID from $attributeCode
        $attributeIdFromParentProduct = [];

        // prepare the array with the variant
        // attribute IDs of the parent product
        foreach ($actualAttributes as $attributeCode) {
            if (isset($allProductAttributes[$attributeCode])) {
                $attributeIdFromParentProduct[] = $allProductAttributes[$attributeCode][MemberNames::ATTRIBUTE_ID];
            }
        }

        // we don't want delete everything
        if (empty($attributeIdFromParentProduct)) {
            return;
        }

        // remove the old super attributes from the database
        $this->getProductVariantProcessor()
             ->deleteProductSuperAttribute(
                 array(
                     MemberNames::PRODUCT_ID   => $parentProductId,
                     MemberNames::ATTRIBUTE_ID => $attributeIdFromParentProduct
                 )
             );

        // log a debug message that the image has been removed
        $this->getSubject()
             ->getSystemLogger()
             ->info(
                 $this->getSubject()->appendExceptionSuffix(
                     sprintf(
                         'Successfully clean up variant attributes for product with SKU "%s"',
                         $parentSku
                     )
                 )
             );
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
