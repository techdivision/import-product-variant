<?php

/**
 * TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeRepositoryInterface
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

namespace TechDivision\Import\Product\Variant\Repositories;

use TechDivision\Import\Dbal\Repositories\RepositoryInterface;

/**
 * Interface for repository implementations to load product super attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
interface ProductSuperAttributeRepositoryInterface extends RepositoryInterface
{

    /**
     * Load's the product super attribute with the passed product/attribute ID.
     *
     * @param integer $productId   The entity ID of the product super attribute's product
     * @param integer $attributeId The attribute ID of the product super attributes attribute
     *
     * @return array The product super attribute
     */
    public function findOneByProductIdAndAttributeId($productId, $attributeId);

    /**
     * Load's the product super attribute with the passed product ID.
     *
     * @param integer $productId The entity ID of the product super attribute's product
     *
     * @return array The product super attributes
     */
    public function findOneByProductId($productId);
}
