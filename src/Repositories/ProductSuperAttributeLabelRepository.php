<?php

/**
 * TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeLabelRepository
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Repositories;

use TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository;
use TechDivision\Import\Product\Variant\Utils\MemberNames;
use TechDivision\Import\Product\Variant\Utils\SqlStatementKeys;

/**
 * Repository implementation to load product super attribute label data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class ProductSuperAttributeLabelRepository extends AbstractRepository implements ProductSuperAttributeLabelRepositoryInterface
{

    /**
     * The prepared statement to load an existing product super attribute label.
     *
     * @var \PDOStatement
     */
    protected $productSuperAttributeLabelStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->productSuperAttributeLabelStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT_SUPER_ATTRIBUTE_LABEL));
    }

    /**
     * Load's the product super attribute label with the passed product super attribute/store ID.
     *
     * @param integer $productSuperAttributeId The product super attribute ID of the product super attribute label
     * @param integer $storeId                 The store ID of the product super attribute label
     *
     * @return array The product super attribute label
     */
    public function findOneByProductSuperAttributeIdAndStoreId($productSuperAttributeId, $storeId)
    {

        // initialize the params
        $params = array(
            MemberNames::PRODUCT_SUPER_ATTRIBUTE_ID => $productSuperAttributeId,
            MemberNames::STORE_ID                   => $storeId
        );

        // load and return the product super attribute label with the passed product super attribute/store ID
        $this->productSuperAttributeLabelStmt->execute($params);
        return $this->productSuperAttributeLabelStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
