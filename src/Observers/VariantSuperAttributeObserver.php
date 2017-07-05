<?php

/**
 * TechDivision\Import\Product\Variant\Observers\VariantSuperAttributeObserver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Observers;

use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Product\Variant\Utils\ColumnKeys;
use TechDivision\Import\Product\Variant\Utils\MemberNames;
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;
use TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface;

/**
 * Oberserver that provides functionality for the product variant super attributes replace operation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class VariantSuperAttributeObserver extends AbstractProductImportObserver
{

    /**
     * The ID of the actual store to use.
     *
     * @var integer
     */
    protected $storeId;

    /**
     * The EAV attribute to handle.
     *
     * @var array
     */
    protected $eavAttribute;

    /**
     * The tempoarary stored product super attribute ID.
     *
     * @var integer
     */
    protected $productSuperAttributeId;

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
     */
    public function __construct(ProductVariantProcessorInterface $productVariantProcessor)
    {
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
     * @return array The processed row
     */
    protected function process()
    {

        // load parent/child IDs
        $parentId = $this->mapParentSku($parentSku = $this->getValue(ColumnKeys::VARIANT_PARENT_SKU));

        // query whether or not, the parent ID have changed
        if ($this->isParentId($parentId)) {
            return;
        }

        // prepare the store view code
        $this->prepareStoreViewCode($this->getRow());

        // preserve the parent ID
        $this->setParentId($parentId);

        // extract the option value and attribute code from the row
        $attributeCode = $this->getValue(ColumnKeys::VARIANT_ATTRIBUTE_CODE);

        // load the store and set the store ID
        $store = $this->getStoreByStoreCode($this->getStoreViewCode(StoreViewCodes::ADMIN));
        $this->storeId = $store[MemberNames::STORE_ID];

        try {
            // load the EAV attribute with the found attribute code
            $this->eavAttribute = $this->getEavAttributeByAttributeCode($attributeCode);
        } catch (\Exception $e) {
            throw $this->wrapException(array(ColumnKeys::VARIANT_ATTRIBUTE_CODE), $e);
        }

        try {
            // initialize and save the super attribute
            $productSuperAttribute = $this->initializeProductSuperAttribute($this->prepareProducSuperAttributeAttributes());
            $this->productSuperAttributeId = $this->persistProductSuperAttribute($productSuperAttribute);

            // initialize and save the super attribute label
            $productSuperAttributeLabel = $this->initializeProductSuperAttributeLabel($this->prepareProductSuperAttributeLabelAttributes());
            $this->persistProductSuperAttributeLabel($productSuperAttributeLabel);

        } catch (\Exception $e) {
            // prepare a more detailed error message
            $message = $this->appendExceptionSuffix(
                sprintf(
                    'Super attribute for SKU %s and attribute %s can\'t be created',
                    $parentSku,
                    $attributeCode
                )
            );

            // if we're NOT in debug mode, re-throw a more detailed exception
            $wrappedException = $this->wrapException(
                array(ColumnKeys::VARIANT_PARENT_SKU, ColumnKeys::VARIANT_ATTRIBUTE_CODE),
                new \Exception($message, null, $e)
            );

            // query whether or not, debug mode is enabled
            if ($this->isDebugMode()) {
                // log a warning and return immediately
                $this->getSystemLogger()->warning($wrappedException->getMessage());
                return;
            }

            // else, throw the exception
            throw $wrappedException;
        }
    }

    /**
     * Prepare the product super attribute attributes that has to be persisted.
     *
     * @return array The prepared product attribute attributes
     */
    protected function prepareProducSuperAttributeAttributes()
    {

        // load the parent ID
        $parentId = $this->getParentId();

        // load the attribute ID
        $attributeId = $this->eavAttribute[MemberNames::ATTRIBUTE_ID];

        // initialize the attributes and return them
        return $this->initializeEntity(
            array(
                MemberNames::PRODUCT_ID   => $parentId,
                MemberNames::ATTRIBUTE_ID => $attributeId,
                MemberNames::POSITION     => 0
            )
        );
    }

    /**
     * Prepare the product super attribute label attributes that has to be persisted.
     *
     * @return array The prepared product super attribute label attributes
     */
    protected function prepareProductSuperAttributeLabelAttributes()
    {

        // extract the parent/child ID as well as option value and variation label from the row
        $variationLabel = $this->getValue(ColumnKeys::VARIANT_VARIATION_LABEL);

        // query whether or not we've to create super attribute labels
        if (empty($variationLabel)) {
            $variationLabel = $this->eavAttribute[MemberNames::FRONTENT_LABEL];
        }

        // initialize the attributes and return them
        return $this->initializeEntity(
            array(
                MemberNames::PRODUCT_SUPER_ATTRIBUTE_ID => $this->productSuperAttributeId,
                MemberNames::STORE_ID                   => $this->storeId,
                MemberNames::USE_DEFAULT                => 0,
                MemberNames::VALUE                      => $variationLabel
            )
        );
    }

    /**
     * Initialize the product super attribute with the passed attributes and returns an instance.
     *
     * @param array $attr The product super attribute attributes
     *
     * @return array The initialized product super attribute
     */
    protected function initializeProductSuperAttribute(array $attr)
    {
        return $attr;
    }

    /**
     * Initialize the product super attribute label with the passed attributes and returns an instance.
     *
     * @param array $attr The product super attribute label attributes
     *
     * @return array The initialized product super attribute label
     */
    protected function initializeProductSuperAttributeLabel(array $attr)
    {
        return $attr;
    }

    /**
     * Map's the passed SKU of the parent product to it's PK.
     *
     * @param string $parentSku The SKU of the parent product
     *
     * @return integer The primary key used to create relations
     */
    protected function mapParentSku($parentSku)
    {
        return $this->mapSkuToEntityId($parentSku);
    }

    /**
     * Return the entity ID for the passed SKU.
     *
     * @param string $sku The SKU to return the entity ID for
     *
     * @return integer The mapped entity ID
     * @throws \Exception Is thrown if the SKU is not mapped yet
     */
    protected function mapSkuToEntityId($sku)
    {
        return $this->getSubject()->mapSkuToEntityId($sku);
    }

    /**
     * Return's TRUE if the passed ID is the parent one.
     *
     * @param integer $parentId The parent ID to check
     *
     * @return boolean TRUE if the passed ID is the parent one
     */
    protected function isParentId($parentId)
    {
        return $this->getParentId() === $parentId;
    }

    /**
     * Set's the ID of the parent product to relate the variant with.
     *
     * @param integer $parentId The ID of the parent product
     *
     * @return void
     */
    protected function setParentId($parentId)
    {
        $this->getSubject()->setParentId($parentId);
    }

    /**
     * Return's the ID of the parent product to relate the variant with.
     *
     * @return integer The ID of the parent product
     */
    protected function getParentId()
    {
        return $this->getSubject()->getParentId();
    }

    /**
     * Return's the store for the passed store code.
     *
     * @param string $storeCode The store code to return the store for
     *
     * @return array The requested store
     * @throws \Exception Is thrown, if the requested store is not available
     */
    protected function getStoreByStoreCode($storeCode)
    {
        return $this->getSubject()->getStoreByStoreCode($storeCode);
    }

    /**
     * Return's an array with the available stores.
     *
     * @return array The available stores
     */
    protected function getStores()
    {
        return $this->getSubject()->getStores();
    }

    /**
     * Return's the first EAV attribute for the passed attribute code.
     *
     * @param string $attributeCode The attribute code
     *
     * @return array The array with the EAV attribute
     */
    protected function getEavAttributeByAttributeCode($attributeCode)
    {
        return $this->getSubject()->getEavAttributeByAttributeCode($attributeCode);
    }

    /**
     * Persist's the passed product super attribute data and return's the ID.
     *
     * @param array $productSuperAttribute The product super attribute data to persist
     *
     * @return void
     */
    protected function persistProductSuperAttribute($productSuperAttribute)
    {
        return $this->getProductVariantProcessor()->persistProductSuperAttribute($productSuperAttribute);
    }

    /**
     * Persist's the passed product super attribute label data and return's the ID.
     *
     * @param array $productSuperAttributeLabel The product super attribute label data to persist
     *
     * @return void
     */
    protected function persistProductSuperAttributeLabel($productSuperAttributeLabel)
    {
        return $this->getProductVariantProcessor()->persistProductSuperAttributeLabel($productSuperAttributeLabel);
    }
}
