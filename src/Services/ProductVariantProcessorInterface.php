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
 * A SLSB providing methods to load product data using a PDO connection.
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
     * Return's the action with the product relation CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductRelationAction The action instance
     */
    public function getProductRelationAction();

    /**
     * Return's the action with the product super attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeAction The action instance
     */
    public function getProductSuperAttributeAction();

    /**
     * Return's the action with the product super attribute label CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeLabelAction The action instance
     */
    public function getProductSuperAttributeLabelAction();

    /**
     * Return's the action with the product super link CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductSuperLinkAction The action instance
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
     * Return's the first EAV attribute for the passed option value and store ID.
     *
     * @param string $optionValue The option value of the EAV attributes
     * @param string $storeId     The store ID of the EAV attribues
     *
     * @return array The array with the EAV attribute
     */
    public function getEavAttributeByOptionValueAndStoreId($optionValue, $storeId);

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
