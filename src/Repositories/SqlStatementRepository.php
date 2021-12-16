<?php

/**
 * TechDivision\Import\Product\Variant\Repositories\SqlStatementRepository
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

use TechDivision\Import\Product\Variant\Utils\SqlStatementKeys;

/**
 * Repository class with the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class SqlStatementRepository extends \TechDivision\Import\Product\Repositories\SqlStatementRepository
{

    /**
     * The SQL statements.
     *
     * @var array
     */
    private $statements = array(
        SqlStatementKeys::PRODUCT_SUPER_LINK =>
            'SELECT *
               FROM ${table:catalog_product_super_link}
              WHERE product_id = :product_id
                AND parent_id = :parent_id',
        SqlStatementKeys::PRODUCT_SUPER_LINK_PARENT =>
            'SELECT *
               FROM ${table:catalog_product_super_link}
              WHERE parent_id = :parent_id',
        SqlStatementKeys::PRODUCT_SUPER_ATTRIBUTE =>
            'SELECT *
               FROM ${table:catalog_product_super_attribute}
              WHERE product_id = :product_id
                AND attribute_id = :attribute_id',
        SqlStatementKeys::PRODUCT_SUPER_ATTRIBUTE_BY_PRODUCT =>
            'SELECT *
               FROM ${table:catalog_product_super_attribute}
              WHERE product_id = :product_id',
        SqlStatementKeys::PRODUCT_SUPER_ATTRIBUTE_LABEL =>
            'SELECT *
               FROM ${table:catalog_product_super_attribute_label}
              WHERE product_super_attribute_id = :product_super_attribute_id
                AND store_id = :store_id',
        SqlStatementKeys::CREATE_PRODUCT_SUPER_LINK =>
            'INSERT ${table:catalog_product_super_link}
                    (${column-names:catalog_product_super_link})
             VALUES (${column-placeholders:catalog_product_super_link})',
        SqlStatementKeys::CREATE_PRODUCT_SUPER_ATTRIBUTE =>
            'INSERT ${table:catalog_product_super_attribute}
                    (${column-names:catalog_product_super_attribute})
             VALUES (${column-placeholders:catalog_product_super_attribute})',
        SqlStatementKeys::UPDATE_PRODUCT_SUPER_ATTRIBUTE =>
            'UPDATE ${table:catalog_product_super_attribute}
                SET ${column-values:catalog_product_super_attribute}
              WHERE product_super_attribute_id = :product_super_attribute_id',
        SqlStatementKeys::CREATE_PRODUCT_SUPER_ATTRIBUTE_LABEL =>
            'INSERT ${table:catalog_product_super_attribute_label}
                    (${column-names:catalog_product_super_attribute_label})
             VALUES (${column-placeholders:catalog_product_super_attribute_label})',
        SqlStatementKeys::UPDATE_PRODUCT_SUPER_ATTRIBUTE_LABEL =>
            'UPDATE ${table:catalog_product_super_attribute_label}
                SET ${column-values:catalog_product_super_attribute_label}
              WHERE value_id = :value_id',
        SqlStatementKeys::DELETE_PRODUCT_SUPER_LINK =>
            'DELETE
               FROM ${table:catalog_product_super_link}
              WHERE parent_id = :parent_id
                AND product_id
             NOT IN (SELECT entity_id FROM catalog_product_entity WHERE sku IN (:skus))',
        SqlStatementKeys::DELETE_PRODUCT_SUPER_ATTRIBUTE =>
            'DELETE
               FROM ${table:catalog_product_super_attribute}
              WHERE product_id = :product_id
                AND attribute_id
             NOT IN (:attribute_ids)',
    );

    /**
     * Initializes the SQL statement repository with the primary key and table prefix utility.
     *
     * @param \IteratorAggregate<\TechDivision\Import\Dbal\Utils\SqlCompilerInterface> $compilers The array with the compiler instances
     */
    public function __construct(\IteratorAggregate $compilers)
    {

        // pass primary key + table prefix utility to parent instance
        parent::__construct($compilers);

        // compile the SQL statements
        $this->compile($this->statements);
    }
}
