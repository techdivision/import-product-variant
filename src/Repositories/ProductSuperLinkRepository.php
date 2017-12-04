<?php

/**
 * TechDivision\Import\Product\Variant\Repositories\ProductSuperLinkRepository
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

namespace TechDivision\Import\Product\Variant\Repositories;

use TechDivision\Import\Repositories\AbstractRepository;
use TechDivision\Import\Product\Variant\Utils\MemberNames;
use TechDivision\Import\Product\Variant\Utils\SqlStatementKeys;

/**
 * Repository implementation to load product super link data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class ProductSuperLinkRepository extends AbstractRepository
{

    /**
     * The prepared statement to load an existing product super link.
     *
     * @var \PDOStatement
     */
    protected $productSuperLinkStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->productSuperLinkStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::PRODUCT_SUPER_LINK));
    }

    /**
     * Load's the product super link with the passed product/parent ID.
     *
     * @param integer $productId The entity ID of the product super link's product
     * @param integer $parentId  The entity ID of the product super link's parent product
     *
     * @return array The product super link
     */
    public function findOneByProductIdAndParentId($productId, $parentId)
    {

        // initialize the params
        $params = array(
            MemberNames::PRODUCT_ID => $productId,
            MemberNames::PARENT_ID  => $parentId,
        );

        // load and return the product super link with the passed product/parent ID
        $this->productSuperLinkStmt->execute($params);
        return $this->productSuperLinkStmt->fetch(\PDO::FETCH_ASSOC);
    }
}
