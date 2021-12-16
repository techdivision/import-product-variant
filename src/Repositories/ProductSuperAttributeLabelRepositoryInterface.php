<?php

/**
 * TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeLabelRepositoryInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Repositories;

use TechDivision\Import\Dbal\Repositories\RepositoryInterface;

/**
 * Interface for repository implementations to load product super attribute label data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
interface ProductSuperAttributeLabelRepositoryInterface extends RepositoryInterface
{

    /**
     * Load's the product super attribute label with the passed product super attribute/store ID.
     *
     * @param integer $productSuperAttributeId The product super attribute ID of the product super attribute label
     * @param integer $storeId                 The store ID of the product super attribute label
     *
     * @return array The product super attribute label
     */
    public function findOneByProductSuperAttributeIdAndStoreId($productSuperAttributeId, $storeId);
}
