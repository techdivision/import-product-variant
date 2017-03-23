<?php

/**
 * TechDivision\Import\Product\Variant\Subjects\VariantSubject
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

namespace TechDivision\Import\Product\Variant\Subjects;

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Product\Subjects\AbstractProductSubject;

/**
 * A SLSB that handles the process to import product variants.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class VariantSubject extends AbstractProductSubject
{

    /**
     * The ID of the parent product to relate the variant with.
     *
     * @var integer
     */
    protected $parentId;

    /**
     * The mapping for the SKUs to the created entity IDs.
     *
     * @var array
     */
    protected $skuEntityIdMapping = array();

    /**
     * Intializes the previously loaded global data for exactly one variants.
     *
     * @return void
     * @see \Importer\Csv\Actions\ProductImportAction::prepare()
     */
    public function setUp()
    {

        // invoke parent method
        parent::setUp();

        // load the entity manager and the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // load the status of the actual import process
        $status = $registryProcessor->getAttribute($this->getSerial());

        // load the attribute set we've prepared intially
        $this->skuEntityIdMapping = $status[RegistryKeys::SKU_ENTITY_ID_MAPPING];
    }

    /**
     * Set's the ID of the parent product to relate the variant with.
     *
     * @param integer $parentId The ID of the parent product
     *
     * @return void
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Return's the ID of the parent product to relate the variant with.
     *
     * @return integer The ID of the parent product
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Return the entity ID for the passed SKU.
     *
     * @param string $sku The SKU to return the entity ID for
     *
     * @return integer The mapped entity ID
     * @throws \Exception Is thrown if the SKU is not mapped yet
     */
    public function mapSkuToEntityId($sku)
    {

        // query weather or not the SKU has been mapped
        if (isset($this->skuEntityIdMapping[$sku])) {
            return $this->skuEntityIdMapping[$sku];
        }

        // throw an exception if the SKU has not been mapped yet
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Found not mapped entity ID for SKU %s', $sku)
            )
        );
    }

    /**
     * Return's the store for the passed store code.
     *
     * @param string $storeCode The store code to return the store for
     *
     * @return array The requested store
     * @throws \Exception Is thrown, if the requested store is not available
     */
    public function getStoreByStoreCode($storeCode)
    {

        // query whether or not the store with the passed store code exists
        if (isset($this->stores[$storeCode])) {
            return $this->stores[$storeCode];
        }

        // throw an exception, if not
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Found invalid store code %s', $storeCode)
            )
        );
    }

    /**
     * Load's the product relation with the passed parent/child ID.
     *
     * @param integer $parentId The entity ID of the product relation's parent product
     * @param integer $childId  The entity ID of the product relation's child product
     *
     * @return array The product relation
     */
    public function loadProductRelation($parentId, $childId)
    {
        return $this->getProductProcessor()->loadProductRelation($parentId, $childId);
    }

    /**
     * Load's the product super link with the passed product/parent ID.
     *
     * @param integer $productId The entity ID of the product super link's product
     * @param integer $parentId  The entity ID of the product super link's parent product
     *
     * @return array The product super link
     */
    public function loadProductSuperLink($productId, $parentId)
    {
        return $this->getProductProcessor()->loadProductSuperLink($productId, $parentId);
    }

    /**
     * Load's the product super attribute with the passed product/attribute ID.
     *
     * @param integer $productId   The entity ID of the product super attribute's product
     * @param integer $attributeId The attribute ID of the product super attributes attribute
     *
     * @return array The product super attribute
     */
    public function loadProductSuperAttribute($productId, $attributeId)
    {
        return $this->getProductProcessor()->loadProductSuperAttribute($productId, $attributeId);
    }

    /**
     * Load's the product super attribute label with the passed product super attribute/store ID.
     *
     * @param integer $productSuperAttributeId The product super attribute ID of the product super attribute label
     * @param integer $storeId                 The store ID of the product super attribute label
     *
     * @return array The product super attribute label
     */
    public function loadProductSuperAttributeLabel($productSuperAttributeId, $storeId)
    {
        return $this->getProductProcessor()->loadProductSuperAttributeLabel($productSuperAttributeId, $storeId);
    }

    /**
     * Persist's the passed product relation data and return's the ID.
     *
     * @param array $productRelation The product relation data to persist
     *
     * @return void
     */
    public function persistProductRelation($productRelation)
    {
        return $this->getProductProcessor()->persistProductRelation($productRelation);
    }

    /**
     * Persist's the passed product super link data and return's the ID.
     *
     * @param array $productSuperLink The product super link data to persist
     *
     * @return void
     */
    public function persistProductSuperLink($productSuperLink)
    {
        return $this->getProductProcessor()->persistProductSuperLink($productSuperLink);
    }

    /**
     * Persist's the passed product super attribute data and return's the ID.
     *
     * @param array $productSuperAttribute The product super attribute data to persist
     *
     * @return string The ID of the persisted product super attribute entity
     */
    public function persistProductSuperAttribute($productSuperAttribute)
    {
        return $this->getProductProcessor()->persistProductSuperAttribute($productSuperAttribute);
    }

    /**
     * Persist's the passed product super attribute label data and return's the ID.
     *
     * @param array $productSuperAttributeLabel The product super attribute label data to persist
     *
     * @return void
     */
    public function persistProductSuperAttributeLabel($productSuperAttributeLabel)
    {
        return $this->getProductProcessor()->persistProductSuperAttributeLabel($productSuperAttributeLabel);
    }
}
