<?php

/**
 * TechDivision\Import\Product\Variant\Utils\TableNames
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Utils;

/**
 * Utility class containing the available change set names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class TableNames
{

    /**
     * The key for change set name for the product super attribute change set.
     *
     * @var string
     */
    const CATALOG_PRODUCT_SUPER_ATTRIBUTE = 'catalog_product_super_attribute';

    /**
     * The key for change set name for the product super attribute label change set.
     *
     * @var string
     */
    const CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL = 'catalog_product_super_attribute_label';
}
