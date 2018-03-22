<?php

/**
 * TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface
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

namespace TechDivision\Import\Product\Variant\Services;

use TechDivision\Import\Product\Services\ProductProcessorInterface;

/**
 * Interface for product variant processor implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
interface ProductVariantProcessorInterface extends ProductProcessorInterface
{

    /**
     * Return's the repository to access EAV attributes.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeRepositoryInterface The repository instance
     */
    public function getEavAttributeRepository();

    /**
     * Return's the repository to access product relations.
     *
     * @return \TechDivision\Import\Product\Variant\Repositories\ProductRelationRepositoryInterface The repository instance
     */
    public function getProductRelationRepository();

    /**
     * Return's the repository to access product super links.
     *
     * @return \TechDivision\Import\Product\Variant\Repositories\ProductSuperLinkRepositoryInterface The repository instance
     */
    public function getProductSuperLinkRepository();

    /**
     * Return's the repository to access product super attributes.
     *
     * @return \TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeRepositoryInterface The repository instance
     */
    public function getProductSuperAttributeRepository();

    /**
     * Return's the repository to access product super attribute labels.
     *
     * @return \TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeLabelRepositoryInterface The repository instance
     */
    public function getProductSuperAttributeLabelRepository();

    /**
     * Return's the repository to access EAV attribute option values.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface The repository instance
     */
    public function getEavAttributeOptionValueRepository();

    /**
     * Return's the action with the product relation CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductRelationActionInterface The action instance
     */
    public function getProductRelationAction();

    /**
     * Return's the action with the product super attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeActionInterface The action instance
     */
    public function getProductSuperAttributeAction();

    /**
     * Return's the action with the product super attribute label CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeLabelActionInterface The action instance
     */
    public function getProductSuperAttributeLabelAction();

    /**
     * Return's the action with the product super link CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductSuperLinkActionInterface The action instance
     */
    public function getProductSuperLinkAction();

    /**
     * Return's the attribute option value with the passed value and store ID.
     *
     * @param mixed   $value   The option value
     * @param integer $storeId The ID of the store
     *
     * @return array|boolean The attribute option value instance
     */
    public function getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId);

    /**
     * Load's the product relation with the passed parent/child ID.
     *
     * @param integer $parentId The entity ID of the product relation's parent product
     * @param integer $childId  The entity ID of the product relation's child product
     *
     * @return array The product relation
     */
    public function loadProductRelation($parentId, $childId);

    /**
     * Load's the product super link with the passed product/parent ID.
     *
     * @param integer $productId The entity ID of the product super link's product
     * @param integer $parentId  The entity ID of the product super link's parent product
     *
     * @return array The product super link
     */
    public function loadProductSuperLink($productId, $parentId);

    /**
     * Load's the product super attribute with the passed product/attribute ID.
     *
     * @param integer $productId   The entity ID of the product super attribute's product
     * @param integer $attributeId The attribute ID of the product super attributes attribute
     *
     * @return array The product super attribute
     */
    public function loadProductSuperAttribute($productId, $attributeId);

    /**
     * Load's the product super attribute label with the passed product super attribute/store ID.
     *
     * @param integer $productSuperAttributeId The product super attribute ID of the product super attribute label
     * @param integer $storeId                 The store ID of the product super attribute label
     *
     * @return array The product super attribute label
     */
    public function loadProductSuperAttributeLabel($productSuperAttributeId, $storeId);

    /**
     * Persist's the passed product relation data and return's the ID.
     *
     * @param array       $productRelation The product relation data to persist
     * @param string|null $name            The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductRelation($productRelation, $name = null);

    /**
     * Persist's the passed product super link data and return's the ID.
     *
     * @param array       $productSuperLink The product super link data to persist
     * @param string|null $name             The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductSuperLink($productSuperLink, $name = null);

    /**
     * Persist's the passed product super attribute data and return's the ID.
     *
     * @param array       $productSuperAttribute The product super attribute data to persist
     * @param string|null $name                  The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted product super attribute entity
     */
    public function persistProductSuperAttribute($productSuperAttribute, $name = null);

    /**
     * Persist's the passed product super attribute label data and return's the ID.
     *
     * @param array       $productSuperAttributeLabel The product super attribute label data to persist
     * @param string|null $name                       The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductSuperAttributeLabel($productSuperAttributeLabel, $name = null);
}
