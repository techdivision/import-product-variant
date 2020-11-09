<?php

/**
 * TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperLinkDeleteProcessor
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
use TechDivision\Import\Actions\Processors\AbstractCreateProcessor;

/**
 * The product super link create processor implementation.
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class ProductSuperLinkDeleteProcessor extends AbstractCreateProcessor
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
        $skus = $row[MemberNames::SKU];
        if (!is_array($skus)) {
            $skus = [$skus];
        }
        // all skus that should not delete
        $vals = implode(',', $skus);
        // format skus as comma separated sql string
        $vals = \str_replace(',', "','", "'".$vals."'");
        // replace placeholder
        $sql = str_replace(
            array(':skus', ':parent_id'),
            array($vals, $row[MemberNames::PARENT_ID]),
            $this->loadStatement(SqlStatementKeys::DELETE_PRODUCT_SUPER_LINK)
        );

        // delete the variants that are NOT in values (take a look at DELETE_PRODUCT_SUPER_LINK definition)
        $this->getConnection()->query($sql);
    }
}
