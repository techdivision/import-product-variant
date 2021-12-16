<?php

/**
 * TechDivision\Import\Product\Variant\Utils\EntityTypeCodes
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Utils;

/**
 * Utility class containing the entity type codes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class EntityTypeCodes extends \TechDivision\Import\Utils\EntityTypeCodes
{

    /**
     * Key for the product entity 'catalog_product_super_attribute'.
     *
     * @var string
     */
    const CATALOG_PRODUCT_SUPER_ATTRIBUTE = 'catalog_product_super_attribute';

    /**
     * Key for the product entity 'catalog_product_super_attribute_label'.
     *
     * @var string
     */
    const CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL = 'catalog_product_super_attribute_label';
}
