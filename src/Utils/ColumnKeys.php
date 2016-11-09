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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Product\Variant\Utils;

use TechDivision\Import\Product\Utils\ColumnKeys as FallbackColumnKeys;

/**
 * Utility class containing the CSV column names.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ColumnKeys extends FallbackColumnKeys
{

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

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
