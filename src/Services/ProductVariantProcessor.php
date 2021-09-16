<?php

/**
 * TechDivision\Import\Product\Variant\Services\ProductVariantProcessor
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Services;

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Dbal\Actions\ActionInterface;
use TechDivision\Import\Dbal\Connection\ConnectionInterface;
use TechDivision\Import\Repositories\EavAttributeRepositoryInterface;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductRelationRepositoryInterface;
use TechDivision\Import\Product\Variant\Repositories\ProductSuperLinkRepositoryInterface;
use TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeRepositoryInterface;
use TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeLabelRepositoryInterface;

/**
 * The product variant processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class ProductVariantProcessor implements ProductVariantProcessorInterface
{

    /**
     * A PDO connection initialized with the values from the Doctrine EntityManager.
     *
     * @var \TechDivision\Import\Dbal\Connection\ConnectionInterface
     */
    protected $connection;

    /**
     * The repository to access product relations.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductRelationRepositoryInterface
     */
    protected $productRelationRepository;

    /**
     * The repository to access product relations.
     *
     * @var \TechDivision\Import\Product\Variant\Repositories\ProductSuperLinkRepositoryInterface
     */
    protected $productSuperLinkRepository;

    /**
     * The repository to access product super attributes.
     *
     * @var \TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeRepositoryInterface
     */
    protected $productSuperAttributeRepository;

    /**
     * The repository to access product super attribute labels.
     *
     * @var \TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeLabelRepositoryInterface
     */
    protected $productSuperAttributeLabelRepository;

    /**
     * The repository to access EAV attributes.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeRepositoryInterface
     */
    protected $eavAttributeRepository;

    /**
     * The repository to access EAV attribute option values.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface
     */
    protected $eavAttributeOptionValueRepository;

    /**
     * The action for product relation CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $productRelationAction;

    /**
     * The action for product super attribute CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $productSuperAttributeAction;

    /**
     * The action for product super attribute label CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $productSuperAttributeLabelAction;

    /**
     * The action for product super link CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $productSuperLinkAction;

    /**
     * The raw entity loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $rawEntityLoader;

    /**
     * Initialize the processor with the necessary assembler and repository instances.
     *
     * @param \TechDivision\Import\Dbal\Connection\ConnectionInterface                                        $connection                           The connection to use
     * @param \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface                    $eavAttributeOptionValueRepository    The EAV attribute option value repository to use
     * @param \TechDivision\Import\Repositories\EavAttributeRepositoryInterface                               $eavAttributeRepository               The EAV attribute repository to use
     * @param \TechDivision\Import\Product\Repositories\ProductRelationRepositoryInterface                    $productRelationRepository            The product relation repository to use
     * @param \TechDivision\Import\Product\Variant\Repositories\ProductSuperLinkRepositoryInterface           $productSuperLinkRepository           The product super link repository to use
     * @param \TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeRepositoryInterface      $productSuperAttributeRepository      The product super attribute repository to use
     * @param \TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeLabelRepositoryInterface $productSuperAttributeLabelRepository The product super attribute label repository to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                                               $productRelationAction                The product relation action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                                               $productSuperLinkAction               The product super link action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                                               $productSuperAttributeAction          The product super attribute action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                                               $productSuperAttributeLabelAction     The product super attribute label action to use
     * @param \TechDivision\Import\Loaders\LoaderInterface                                                    $rawEntityLoader                      The raw entity loader instance
     */
    public function __construct(
        ConnectionInterface $connection,
        EavAttributeOptionValueRepositoryInterface $eavAttributeOptionValueRepository,
        EavAttributeRepositoryInterface $eavAttributeRepository,
        ProductRelationRepositoryInterface $productRelationRepository,
        ProductSuperLinkRepositoryInterface $productSuperLinkRepository,
        ProductSuperAttributeRepositoryInterface $productSuperAttributeRepository,
        ProductSuperAttributeLabelRepositoryInterface $productSuperAttributeLabelRepository,
        ActionInterface $productRelationAction,
        ActionInterface $productSuperLinkAction,
        ActionInterface $productSuperAttributeAction,
        ActionInterface $productSuperAttributeLabelAction,
        LoaderInterface $rawEntityLoader
    ) {
        $this->setConnection($connection);
        $this->setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository);
        $this->setEavAttributeRepository($eavAttributeRepository);
        $this->setProductRelationRepository($productRelationRepository);
        $this->setProductSuperLinkRepository($productSuperLinkRepository);
        $this->setProductSuperAttributeRepository($productSuperAttributeRepository);
        $this->setProductSuperAttributeLabelRepository($productSuperAttributeLabelRepository);
        $this->setProductRelationAction($productRelationAction);
        $this->setProductSuperLinkAction($productSuperLinkAction);
        $this->setProductSuperAttributeAction($productSuperAttributeAction);
        $this->setProductSuperAttributeLabelAction($productSuperAttributeLabelAction);
        $this->setRawEntityLoader($rawEntityLoader);
    }

    /**
     * Set's the raw entity loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $rawEntityLoader The raw entity loader instance to set
     *
     * @return void
     */
    public function setRawEntityLoader(LoaderInterface $rawEntityLoader)
    {
        $this->rawEntityLoader = $rawEntityLoader;
    }

    /**
     * Return's the raw entity loader instance.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The raw entity loader instance
     */
    public function getRawEntityLoader()
    {
        return $this->rawEntityLoader;
    }

    /**
     * Set's the passed connection.
     *
     * @param \TechDivision\Import\Dbal\Connection\ConnectionInterface $connection The connection to set
     *
     * @return void
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return's the connection.
     *
     * @return \TechDivision\Import\Dbal\Connection\ConnectionInterface The connection instance
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO
     * object instance are not committed until you end the transaction by calling ProductProcessor::commit().
     * Calling ProductProcessor::rollBack() will roll back all changes to the database and return the connection
     * to autocommit mode.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.begintransaction.php
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commits a transaction, returning the database connection to autocommit mode until the next call to
     * ProductProcessor::beginTransaction() starts a new transaction.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.commit.php
     */
    public function commit()
    {
        return $this->connection->commit();
    }

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
    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    /**
     * Set's the repository to access EAV attributes.
     *
     * @param \TechDivision\Import\Repositories\EavAttributeRepositoryInterface $eavAttributeRepository The repository to access EAV attributes
     *
     * @return void
     */
    public function setEavAttributeRepository(EavAttributeRepositoryInterface $eavAttributeRepository)
    {
        $this->eavAttributeRepository = $eavAttributeRepository;
    }

    /**
     * Return's the repository to access EAV attributes.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeRepositoryInterface The repository instance
     */
    public function getEavAttributeRepository()
    {
        return $this->eavAttributeRepository;
    }

    /**
     * Set's the repository to access product relations.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductRelationRepositoryInterface $productRelationRepository The repository instance
     *
     * @return void
     */
    public function setProductRelationRepository(ProductRelationRepositoryInterface $productRelationRepository)
    {
        $this->productRelationRepository = $productRelationRepository;
    }

    /**
     * Return's the repository to access product relations.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductRelationRepositoryInterface The repository instance
     */
    public function getProductRelationRepository()
    {
        return $this->productRelationRepository;
    }

    /**
     * Set's the repository to access product super links.
     *
     * @param \TechDivision\Import\Product\Variant\Repositories\ProductSuperLinkRepositoryInterface $productSuperLinkRepository The repository instance
     *
     * @return void
     */
    public function setProductSuperLinkRepository(ProductSuperLinkRepositoryInterface $productSuperLinkRepository)
    {
        $this->productSuperLinkRepository = $productSuperLinkRepository;
    }

    /**
     * Return's the repository to access product super links.
     *
     * @return \TechDivision\Import\Product\Variant\Repositories\ProductSuperLinkRepositoryInterface The repository instance
     */
    public function getProductSuperLinkRepository()
    {
        return $this->productSuperLinkRepository;
    }

    /**
     * Set's the repository to access product super attributes.
     *
     * @param \TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeRepositoryInterface $productSuperAttributeRepository The repository instance
     *
     * @return void
     */
    public function setProductSuperAttributeRepository(ProductSuperAttributeRepositoryInterface $productSuperAttributeRepository)
    {
        $this->productSuperAttributeRepository = $productSuperAttributeRepository;
    }

    /**
     * Return's the repository to access product super attributes.
     *
     * @return \TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeRepositoryInterface The repository instance
     */
    public function getProductSuperAttributeRepository()
    {
        return $this->productSuperAttributeRepository;
    }

    /**
     * Set's the repository to access product super attribute labels.
     *
     * @param \TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeLabelRepositoryInterface $productSuperAttributeLabelRepository The repository instance
     *
     * @return void
     */
    public function setProductSuperAttributeLabelRepository(ProductSuperAttributeLabelRepositoryInterface $productSuperAttributeLabelRepository)
    {
        $this->productSuperAttributeLabelRepository = $productSuperAttributeLabelRepository;
    }

    /**
     * Return's the repository to access product super attribute labels.
     *
     * @return \TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeLabelRepositoryInterface The repository instance
     */
    public function getProductSuperAttributeLabelRepository()
    {
        return $this->productSuperAttributeLabelRepository;
    }

    /**
     * Set's the repository to access EAV attribute option values.
     *
     * @param \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface $eavAttributeOptionValueRepository The repository to access EAV attribute option values
     *
     * @return void
     */
    public function setEavAttributeOptionValueRepository(EavAttributeOptionValueRepositoryInterface $eavAttributeOptionValueRepository)
    {
        $this->eavAttributeOptionValueRepository = $eavAttributeOptionValueRepository;
    }

    /**
     * Return's the repository to access EAV attribute option values.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface The repository instance
     */
    public function getEavAttributeOptionValueRepository()
    {
        return $this->eavAttributeOptionValueRepository;
    }

    /**
     * Set's the action with the product relation CRUD methods.
     *
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface $productRelationAction The action with the product relation CRUD methods
     *
     * @return void
     */
    public function setProductRelationAction(ActionInterface $productRelationAction)
    {
        $this->productRelationAction = $productRelationAction;
    }

    /**
     * Return's the action with the product relation CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductRelationAction()
    {
        return $this->productRelationAction;
    }

    /**
     * Set's the action with the product super attribute CRUD methods.
     *
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface $productSuperAttributeAction The action with the product super attribute CRUD methods
     *
     * @return void
     */
    public function setProductSuperAttributeAction(ActionInterface $productSuperAttributeAction)
    {
        $this->productSuperAttributeAction = $productSuperAttributeAction;
    }

    /**
     * Return's the action with the product super attribute CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductSuperAttributeAction()
    {
        return $this->productSuperAttributeAction;
    }

    /**
     * Set's the action with the product super attribute label CRUD methods.
     *
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface $productSuperAttributeLabelAction The action with the product super attribute label CRUD methods
     *
     * @return void
     */
    public function setProductSuperAttributeLabelAction(ActionInterface $productSuperAttributeLabelAction)
    {
        $this->productSuperAttributeLabelAction = $productSuperAttributeLabelAction;
    }

    /**
     * Return's the action with the product super attribute label CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductSuperAttributeLabelAction()
    {
        return $this->productSuperAttributeLabelAction;
    }

    /**
     * Set's the action with the product super link CRUD methods.
     *
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface $productSuperLinkAction The action with the product super link CRUD methods
     *
     * @return void
     */
    public function setProductSuperLinkAction(ActionInterface $productSuperLinkAction)
    {
        $this->productSuperLinkAction = $productSuperLinkAction;
    }

    /**
     * Return's the action with the product super link CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductSuperLinkAction()
    {
        return $this->productSuperLinkAction;
    }

    /**
     * Return's the attribute option value with the passed value and store ID.
     *
     * @param mixed   $value   The option value
     * @param integer $storeId The ID of the store
     *
     * @return array|boolean The attribute option value instance
     */
    public function getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId)
    {
        return $this->getEavAttributeOptionValueRepository()->findEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId);
    }

    /**
     * Load's and return's a raw entity without primary key but the mandatory members only and nulled values.
     *
     * @param string $entityTypeCode The entity type code to return the raw entity for
     * @param array  $data           An array with data that will be used to initialize the raw entity with
     *
     * @return array The initialized entity
     */
    public function loadRawEntity($entityTypeCode, array $data = array())
    {
        return $this->getRawEntityLoader()->load($entityTypeCode, $data);
    }

    /**
     * Load's the product relation with the passed parent/child ID.
     *
     * @param integer $parentId The entity ID of the product relation's parent product
     * @param integer $childId  The entity ID of the product relation's child product
     *
     * @return array The product relation
     */
    public function loadProductRelation($parentId, $childId)
    {
        return $this->getProductRelationRepository()->findOneByParentIdAndChildId($parentId, $childId);
    }

    /**
     * Load's the product super link with the passed product/parent ID.
     *
     * @param integer $parentId The entity ID of the product super link's parent product
     *
     * @return array The product super link
     */
    public function loadProductSuperLinksFromParent($parentId)
    {
        return $this->getProductSuperLinkRepository()->findAllByParentId($parentId);
    }

    /**
     * Load's the product super link with the passed product/parent ID.
     *
     * @param integer $productId The entity ID of the product super link's product
     * @param integer $parentId  The entity ID of the product super link's parent product
     *
     * @return array The product super link
     */
    public function loadProductSuperLink($productId, $parentId)
    {
        return $this->getProductSuperLinkRepository()->findOneByProductIdAndParentId($productId, $parentId);
    }

    /**
     * Load's the product super attribute with the passed product/attribute ID.
     *
     * @param integer $productId   The entity ID of the product super attribute's product
     * @param integer $attributeId The attribute ID of the product super attributes attribute
     *
     * @return array The product super attribute
     */
    public function loadProductSuperAttribute($productId, $attributeId)
    {
        return $this->getProductSuperAttributeRepository()->findOneByProductIdAndAttributeId($productId, $attributeId);
    }

    /**
     * Load's the product super attributes with the passed product ID.
     *
     * @param integer $productId The entity ID of the product super attribute's product
     *
     * @return array The product super attributes
     */
    public function loadProductSuperAttributesFromProduct($productId)
    {
        return $this->getProductSuperAttributeRepository()->findOneByProductId($productId);
    }

    /**
     * Load's the product super attribute label with the passed product super attribute/store ID.
     *
     * @param integer $productSuperAttributeId The product super attribute ID of the product super attribute label
     * @param integer $storeId                 The store ID of the product super attribute label
     *
     * @return array The product super attribute label
     */
    public function loadProductSuperAttributeLabel($productSuperAttributeId, $storeId)
    {
        return $this->getProductSuperAttributeLabelRepository()->findOneByProductSuperAttributeIdAndStoreId($productSuperAttributeId, $storeId);
    }

    /**
     * Persist's the passed product relation data and return's the ID.
     *
     * @param array       $productRelation The product relation data to persist
     * @param string|null $name            The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductRelation($productRelation, $name = null)
    {
        return $this->getProductRelationAction()->persist($productRelation, $name);
    }

    /**
     * Persist's the passed product super link data and return's the ID.
     *
     * @param array       $productSuperLink The product super link data to persist
     * @param string|null $name             The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductSuperLink($productSuperLink, $name = null)
    {
        return $this->getProductSuperLinkAction()->persist($productSuperLink, $name);
    }

    /**
     * Persist's the passed product super attribute data and return's the ID.
     *
     * @param array       $productSuperAttribute The product super attribute data to persist
     * @param string|null $name                  The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted product super attribute entity
     */
    public function persistProductSuperAttribute($productSuperAttribute, $name = null)
    {
        return $this->getProductSuperAttributeAction()->persist($productSuperAttribute, $name);
    }

    /**
     * Persist's the passed product super attribute label data and return's the ID.
     *
     * @param array       $productSuperAttributeLabel The product super attribute label data to persist
     * @param string|null $name                       The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductSuperAttributeLabel($productSuperAttributeLabel, $name = null)
    {
        return $this->getProductSuperAttributeLabelAction()->persist($productSuperAttributeLabel, $name);
    }

    /**
     * Delete's the passed product link data.
     *
     * @param array       $row  The product link to be deleted
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted entity
     */
    public function deleteProductSuperLink(array $row, $name = null)
    {
        return $this->getProductSuperLinkAction()->delete($row, $name);
    }

    /**
     * Delete's the passed product super attribute data
     *
     * @param array       $row  The product super attribute id to be deleted
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted product super attribute entity
     */
    public function deleteProductSuperAttribute(array $row, $name = null)
    {
        return $this->getProductSuperAttributeAction()->delete($row, $name);
    }
}
