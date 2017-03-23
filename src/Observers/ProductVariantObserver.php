<?php

/**
 * TechDivision\Import\Product\Variant\Observers\ProductVariantObserver
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

use TechDivision\Import\Utils\ProductTypes;
use TechDivision\Import\Product\Variant\Utils\ColumnKeys;
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class ProductVariantObserver extends AbstractProductImportObserver
{

    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'variants';

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not we've found a configurable product
        if ($this->getValue(ColumnKeys::PRODUCT_TYPE) !== ProductTypes::CONFIGURABLE) {
            return;
        }

        // query whether or not, we've configurables
        if ($configurableVariations = $this->getValue(ColumnKeys::CONFIGURABLE_VARIATIONS)) {
            // load the variation labels, if available
            $configurableVariationLabels = $this->getValue(ColumnKeys::CONFIGURABLE_VARIATION_LABELS);

            // create an array with the variation labels (attribute code as key)
            $varLabels = array();
            foreach ($this->explode($configurableVariationLabels, '|') as $variationLabel) {
                if (strstr($variationLabel, '=')) {
                    list ($key, $value) = $this->explode($variationLabel, '=');
                    $varLabels[$key] = $value;
                }
            }

            // intialize the array for the variations
            $artefacts = array();

            // load the parent SKU from the row
            $parentSku = $this->getValue(ColumnKeys::SKU);

            // load the store view code
            $storeViewCode = $this->getValue(ColumnKeys::STORE_VIEW_CODE);

            // load the product's attribute set code
            $attributeSetCode = $this->getValue(ColumnKeys::ATTRIBUTE_SET_CODE);

            // iterate over all variations and import them
            foreach ($this->explode($configurableVariations, '|') as $variation) {
                // sku=Configurable Product 48-option 2,configurable_variation=option 2
                list ($sku, $option) = $this->explode($variation);

                // explode the variations child ID as well as option code and value
                list (, $childSku) = $this->explode($sku, '=');
                list ($optionCode, $optionValue) = $this->explode($option, '=');

                // load the apropriate variation label
                $varLabel = '';
                if (isset($varLabels[$optionCode])) {
                    $varLabel = $varLabels[$optionCode];
                }

                // initialize the product variation itself
                $variation = $this->newArtefact(
                    array(
                        ColumnKeys::STORE_VIEW_CODE         => $storeViewCode,
                        ColumnKeys::ATTRIBUTE_SET_CODE      => $attributeSetCode,
                        ColumnKeys::VARIANT_PARENT_SKU      => $parentSku,
                        ColumnKeys::VARIANT_CHILD_SKU       => $childSku,
                        ColumnKeys::VARIANT_ATTRIBUTE_CODE  => $optionCode,
                        ColumnKeys::VARIANT_OPTION_VALUE    => $optionValue,
                        ColumnKeys::VARIANT_VARIATION_LABEL => $varLabel
                    ),
                    array(
                        ColumnKeys::STORE_VIEW_CODE         => ColumnKeys::STORE_VIEW_CODE,
                        ColumnKeys::ATTRIBUTE_SET_CODE      => ColumnKeys::ATTRIBUTE_SET_CODE,
                        ColumnKeys::VARIANT_PARENT_SKU      => ColumnKeys::SKU,
                        ColumnKeys::VARIANT_CHILD_SKU       => ColumnKeys::CONFIGURABLE_VARIATIONS,
                        ColumnKeys::VARIANT_ATTRIBUTE_CODE  => ColumnKeys::CONFIGURABLE_VARIATIONS,
                        ColumnKeys::VARIANT_OPTION_VALUE    => ColumnKeys::CONFIGURABLE_VARIATIONS,
                        ColumnKeys::VARIANT_VARIATION_LABEL => ColumnKeys::CONFIGURABLE_VARIATION_LABELS
                    )
                );

                // append the product variation
                $artefacts[] = $variation;
            }

            // append the variations to the subject
            $this->addArtefacts($artefacts);
        }
    }

    /**
     * Create's and return's a new empty artefact entity.
     *
     * @param array $columns             The array with the column data
     * @param array $originalColumnNames The array with a mapping from the old to the new column names
     *
     * @return array The new artefact entity
     */
    protected function newArtefact(array $columns, array $originalColumnNames)
    {
        return $this->getSubject()->newArtefact($columns, $originalColumnNames);
    }

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param array $artefacts The product type artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Product\Variant\Subjects\BunchSubject::getLastEntityId()
     */
    protected function addArtefacts(array $artefacts)
    {
        $this->getSubject()->addArtefacts(ProductVariantObserver::ARTEFACT_TYPE, $artefacts);
    }
}
