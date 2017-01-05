<?php

/**
 * TechDivision\Import\Product\Variant\Utils\ColumnKeys
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

namespace TechDivision\Import\Product\Variant\Utils;

/**
 * Utility class containing the CSV column names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class ColumnKeys extends \TechDivision\Import\Product\Utils\ColumnKeys
{

    /**
     * Name for the column 'configurable_variations'.
     *
     * @var string
     */
    const CONFIGURABLE_VARIATIONS = 'configurable_variations';

    /**
     * Name for the column 'configurable_variation_labels'.
     *
     * @var string
     */
    const CONFIGURABLE_VARIATION_LABELS = 'configurable_variation_labels';

    /**
     * Name for the column 'variant_parent_sku'.
     *
     * @var string
     */
    const VARIANT_PARENT_SKU = 'variant_parent_sku';

    /**
     * Name for the column 'variant_child_sku'.
     *
     * @var string
     */
    const VARIANT_CHILD_SKU = 'variant_child_sku';

    /**
     * Name for the column 'variant_option_value'.
     *
     * @var string
     */
    const VARIANT_OPTION_VALUE = 'variant_option_value';

    /**
     * Name for the column 'variant_variation_label'.
     *
     * @var string
     */
    const VARIANT_VARIATION_LABEL = 'variant_variation_label';
}
