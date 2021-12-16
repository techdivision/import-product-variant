<?php

/**
 * TechDivision\Import\Product\Variant\Utils\ColumnKeys
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Utils;

/**
 * Utility class containing the CSV column names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
     * Name for the column 'configurable_variations_position'.
     *
     * @var string
     */
    const CONFIGURABLE_VARIATIONS_POSITION = 'configurable_variations_position';

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
     * Name for the column 'variant_attribute_code'.
     *
     * @var string
     */
    const VARIANT_ATTRIBUTE_CODE = 'variant_attribute_code';

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

    /**
     * Name for the column 'variant_variation_position'.
     *
     * @var string
     */
    const VARIANT_VARIATION_POSITION = 'variant_variation_position';

    /**
     * Name for the column 'variant_variation_use_default'.
     *
     * @var string
     */
    const VARIANT_VARIATION_USE_DEFAULT = 'variant_variation_use_default';
}
