<?php

/**
 * TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Product\Variant\Services;

/**
 * A SLSB providing methods to load product data using a PDO connection.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
interface ProductVariantProcessorInterface
{

    /**
     * Return's the connection.
     *
     * @return \PDO The connection instance
     */
    public function getConnection();

    /**
     * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO
     * object instance are not committed until you end the transaction by calling ProductProcessor::commit().
     * Calling ProductProcessor::rollBack() will roll back all changes to the database and return the connection
     * to autocommit mode.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.begintransaction.php
     */
    public function beginTransaction();

    /**
     * Commits a transaction, returning the database connection to autocommit mode until the next call to
     * ProductProcessor::beginTransaction() starts a new transaction.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.commit.php
     */
    public function commit();

    /**
     * Rolls back the current transaction, as initiated by ProductProcessor::beginTransaction().
     *
     * If the database was set to autocommit mode, this function will restore autocommit mode after it has
     * rolled back the transaction.
     *
     * Some databases, including MySQL, automatically issue an implicit COMMIT when a database definition
     * language (DDL) statement such as DROP TABLE or CREATE TABLE is issued within a transaction. The implicit
     * COMMIT will prevent you from rolling back any other changes within the transaction boundary.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.rollback.php
     */
    public function rollBack();

    /**
     * Return's the action with the product relation CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductRelationAction The action instance
     */
    public function getProductRelationAction();

    /**
     * Return's the action with the product super attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeAction The action instance
     */
    public function getProductSuperAttributeAction();

    /**
     * Return's the action with the product super attribute label CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeLabelAction The action instance
     */
    public function getProductSuperAttributeLabelAction();

    /**
     * Return's the action with the product super link CRUD methods.
     *
     * @return \TechDivision\Import\Product\Variant\Actions\ProductSuperLinkAction The action instance
     */
    public function getProductSuperLinkAction();

    /**
     * Persist's the passed product relation data and return's the ID.
     *
     * @param array $product The product relation data to persist
     *
     * @return void
     */
    public function persistProductRelation($productRelation);

    /**
     * Persist's the passed product super link data and return's the ID.
     *
     * @param array $productSuperLink The product super link data to persist
     *
     * @return void
     */
    public function persistProductSuperLink($productSuperLink);

    /**
     * Persist's the passed product super attribute data and return's the ID.
     *
     * @param array $productSuperAttribute The product super attribute data to persist
     *
     * @return string The ID of the persisted product super attribute entity
     */
    public function persistProductSuperAttribute($productSuperAttribute);

    /**
     * Persist's the passed product super attribute label data and return's the ID.
     *
     * @param array $productSuperAttributeLabel The product super attribute label data to persist
     *
     * @return void
     */
    public function persistProductSuperAttributeLabel($productSuperAttributeLabel);
}
