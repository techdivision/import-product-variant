<?php

/**
 * TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeDeleteProcessor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Actions\Processors;

use TechDivision\Import\Product\Variant\Utils\MemberNames;
use TechDivision\Import\Product\Variant\Utils\SqlStatementKeys;
use TechDivision\Import\Actions\Processors\AbstractDeleteProcessor;

/**
 * The product super attribute update processor implementation.
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class ProductSuperAttributeDeleteProcessor extends AbstractDeleteProcessor
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
