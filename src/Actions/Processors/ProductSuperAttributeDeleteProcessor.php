<?php

/**
 * TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeDeleteProcessor
 *
 * PHP version 7
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Actions\Processors;

use TechDivision\Import\Product\Variant\Utils\MemberNames;
use TechDivision\Import\Product\Variant\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Actions\Processors\AbstractBaseProcessor;

/**
 * The product super attribute update processor implementation.
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class ProductSuperAttributeDeleteProcessor extends AbstractBaseProcessor
{

    /**
     * Delete all variants that are not actual imported
     *
     * @param array       $row                  The row to persist
     * @param string|null $name                 The name of the prepared statement that has to be executed
     * @param string|null $primaryKeyMemberName The primary key member name of the entity to use
     *
     * @return void
     */
    public function execute($row, $name = null, $primaryKeyMemberName = null)
    {

        // load the attribute IDs from the row
        $attributeIds = $row[MemberNames::ATTRIBUTE_ID];

        // make sure we've an array
        if (!is_array($attributeIds)) {
            $attributeIds = [$attributeIds];
        }

        // all attribute IDs that should NOT be deleted
        $vals = implode(',', $attributeIds);

        // replace the placeholders
        $sql = str_replace(
            array(':attribute_ids', ':product_id'),
            array($vals, $row[MemberNames::PRODUCT_ID]),
            $this->loadStatement(SqlStatementKeys::DELETE_PRODUCT_SUPER_ATTRIBUTE)
        );

        // delete the variants that are NOT in values (take a look at DELETE_PRODUCT_SUPER_ATTRIBUTE definition)
        $this->getConnection()->query($sql);
    }
}
