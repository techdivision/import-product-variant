<?php
/**
 * Copyright (c) 2024 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at TechDivision GmbH, Germany
 * For more information see https://www.techdivision.com
 *
 * To obtain a valid license for using this software, please contact us at
 * license@techdivision.com
 */
declare(strict_types=1);

namespace TechDivision\Import\Product\Variant\Actions\Processors;

use TechDivision\Import\Dbal\Collection\Actions\Processors\AbstractBaseProcessor;
use TechDivision\Import\Product\Variant\Utils\MemberNames;
use TechDivision\Import\Product\Variant\Utils\SqlStatementKeys;

/**
 * @copyright Copyright (c) 2024 TechDivision GmbH <info@techdivision.com> - TechDivision GmbH
 * @link http://www.techdivision.com
 * @author MET <met@techdivision.com>
 */
class ProductRelationDeleteProcessor extends AbstractBaseProcessor
{
    /**
     * Delete all relations that are not imported.
     *
     * @param array $row The row to persist
     * @param string|null $name The name of the prepared statement that has to be executed
     * @param string|null $primaryKeyMemberName The primary key member name of the entity to use
     *
     * @return void
     */
    public function execute($row, $name = null, $primaryKeyMemberName = null): void
    {
        // load the SKUs from the row
        $skus = $row[MemberNames::SKU];

        // make sure we've an array
        if (!is_array($skus)) {
            $skus = [$skus];
        }

        // all SKUs that should NOT be deleted
        $vals = implode(',', $skus);

        // concatenate the SKUs as comma separated SQL string
        $vals = str_replace(',', "','", sprintf("'%s'", $vals));

        // replace the placeholders
        $sql = str_replace(
            [':parent_id', ':skus'],
            [$row[MemberNames::PARENT_ID], $vals],
            $this->loadStatement(SqlStatementKeys::DELETE_PRODUCT_RELATION)
        );

        $this->getConnection()->query($sql);
    }
}
