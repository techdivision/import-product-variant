<?php

/**
 * TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeRepository
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
 * Repository implementation to load product super attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class ProductSuperAttributeRepository extends AbstractRepository implements ProductSuperAttributeRepositoryInterface
{

    /**
     * The prepared statement to load an existing product super attribute.
     *
     * @var \PDOStatement
     */
    protected $productSuperAttributeStmt;

    /**
     * @var \PDOStatement
     */
    private $productSuperAttributeByProductStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->productSuperAttributeStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT_SUPER_ATTRIBUTE));
        $this->productSuperAttributeByProductStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT_SUPER_ATTRIBUTE_BY_PRODUCT));
    }

    /**
     * Load's the product super attribute with the passed product/attribute ID.
     *
     * @param integer $productId   The entity ID of the product super attribute's product
     * @param integer $attributeId The attribute ID of the product super attributes attribute
     *
     * @return array The product super attribute
     */
    public function findOneByProductIdAndAttributeId($productId, $attributeId)
    {

        // initialize the params
        $params = array(
            MemberNames::PRODUCT_ID    => $productId,
            MemberNames::ATTRIBUTE_ID  => $attributeId
        );

        // load and return the product super attribute with the passed product/attribute ID
        $this->productSuperAttributeStmt->execute($params);
        return $this->productSuperAttributeStmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Load's the product super attribute with the passed product/attribute ID.
     *
     * @param integer $productId The entity ID of the product super attribute's product
     *
     * @return array The product super attribute
     */
    public function findOneByProductId($productId)
    {

        // initialize the params
        $params = array(
            MemberNames::PRODUCT_ID  => $productId,
        );

        // load and return the product super attributes with the passed product ID
        $this->productSuperAttributeByProductStmt->execute($params);
        return $this->productSuperAttributeByProductStmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
