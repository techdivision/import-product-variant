<?php

/**
 * TechDivision\Import\Product\Variant\Observers\ProductVariantObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Observers;

use TechDivision\Import\Utils\ProductTypes;
use TechDivision\Import\Product\Variant\Utils\ColumnKeys;
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;

/**
 * The observer that exports the data that is necessary to create the variations to a separate CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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

            // explode the variations labels
            if ($variationLabels = $this->explode($configurableVariationLabels)) {
                foreach ($variationLabels as $variationLabel) {
                    if (strstr($variationLabel, '=')) {
                        list ($key, $value) = $this->explode($variationLabel, '=');
                        $varLabels[$key] = $value;
                    }
                }
            }

            // load the variation positions, if available
            $configurableVariationsPosition = $this->getValue(ColumnKeys::CONFIGURABLE_VARIATIONS_POSITION);

            // create an array with the variation labels (attribute code as key)
            $varPositions = array();

            // explode the variations labels
            if ($variationPositions = $this->explode($configurableVariationsPosition)) {
                foreach ($variationPositions as $variationPosition) {
                    if (strstr($variationPosition, '=')) {
                        list ($key, $value) = $this->explode($variationPosition, '=');
                        $varPositions[$key] = $value;
                    }
                }
            }

            // intialize the array for the variations
            $artefacts = array();

            // load the parent SKU, the store view code and the attribute set code from the row
            $parentSku = $this->getValue(ColumnKeys::SKU);
            $storeViewCode = $this->getValue(ColumnKeys::STORE_VIEW_CODE);
            $attributeSetCode = $this->getValue(ColumnKeys::ATTRIBUTE_SET_CODE);

            // iterate over all variations and import them, e. g. the complete value will look like
            // sku=sku-0-black-55 cm,color=Black,size=55 cm| \
            //   sku=sku-01-black-xs,color=Black,size=XS| \
            //   sku=sku-02-blue-xs,color=Blue,size=XS| \
            //   sku=02-blue-55 cm,color=Blue,size=55 cm
            foreach ($this->explode($configurableVariations, '|') as $variation) {
                // explode the SKU and the configurable attribute values, e. g.
                // sku=sku-0-black-55 cm,color=Black,size=55 cm
                $explodedVariation = $this->explode($variation);

                // explode the variations child SKU
                list (, $childSku) = $this->explode(array_shift($explodedVariation), '=');

                // iterate over the configurable attribute configuration
                foreach ($explodedVariation as $option) {
                    // explode the attributes option code and value
                    list ($optionCode, $optionValue) = $this->explode($option, '=');

                    // load the apropriate variation label
                    $varLabel = '';
                    if (isset($varLabels[$optionCode])) {
                        $varLabel = $varLabels[$optionCode];
                    }

                    // load the apropriate variation position
                    $varPosition = null;
                    if (isset($varPositions[$optionCode])) {
                        $varPosition = $varPositions[$optionCode];
                    }

                    // initialize the product variation itself
                    $variation = $this->newArtefact(
                        array(
                            ColumnKeys::STORE_VIEW_CODE            => $storeViewCode,
                            ColumnKeys::ATTRIBUTE_SET_CODE         => $attributeSetCode,
                            ColumnKeys::VARIANT_PARENT_SKU         => $parentSku,
                            ColumnKeys::VARIANT_CHILD_SKU          => $childSku,
                            ColumnKeys::VARIANT_ATTRIBUTE_CODE     => $optionCode,
                            ColumnKeys::VARIANT_OPTION_VALUE       => $optionValue,
                            ColumnKeys::VARIANT_VARIATION_LABEL    => $varLabel,
                            ColumnKeys::VARIANT_VARIATION_POSITION => $varPosition
                        ),
                        array(
                            ColumnKeys::STORE_VIEW_CODE            => ColumnKeys::STORE_VIEW_CODE,
                            ColumnKeys::ATTRIBUTE_SET_CODE         => ColumnKeys::ATTRIBUTE_SET_CODE,
                            ColumnKeys::VARIANT_PARENT_SKU         => ColumnKeys::SKU,
                            ColumnKeys::VARIANT_CHILD_SKU          => ColumnKeys::CONFIGURABLE_VARIATIONS,
                            ColumnKeys::VARIANT_ATTRIBUTE_CODE     => ColumnKeys::CONFIGURABLE_VARIATIONS,
                            ColumnKeys::VARIANT_OPTION_VALUE       => ColumnKeys::CONFIGURABLE_VARIATIONS,
                            ColumnKeys::VARIANT_VARIATION_LABEL    => ColumnKeys::CONFIGURABLE_VARIATION_LABELS,
                            ColumnKeys::VARIANT_VARIATION_POSITION => ColumnKeys::CONFIGURABLE_VARIATIONS_POSITION
                        )
                    );

                    // append the product variation
                    $artefacts[] = $variation;
                }
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
