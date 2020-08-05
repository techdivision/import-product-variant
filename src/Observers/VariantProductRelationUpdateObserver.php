<?php

/**
 * TechDivision\Import\Product\Variant\Observers\VariantProductRelationUpdateObserver
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

use TechDivision\Import\Product\Variant\Utils\ColumnKeys;
use TechDivision\Import\Product\Observers\AbstractProductRelationUpdateObserver;

/**
 * Oberserver that provides functionality for the variant product relation add/update operation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class VariantProductRelationUpdateObserver extends AbstractProductRelationUpdateObserver
{

    /**
     * Returns the column name with the parent SKU.
     *
     * @return string The column name with the parent SKU
     */
    protected function getParentSkuColumnName()
    {
        return ColumnKeys::VARIANT_PARENT_SKU;
    }

    /**
     * Returns the column name with the child SKU.
     *
     * @return string The column name with the child SKU
     */
    protected function getChildSkuColumnName()
    {
        return ColumnKeys::VARIANT_CHILD_SKU;
    }
}
