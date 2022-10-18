<?php

/**
 * TechDivision\Import\Product\Variant\Observers\VariantSuperAttributeObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Observers;

use TechDivision\Import\Dbal\Utils\EntityStatus;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Utils\BackendTypeKeys;
use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Observers\AttributeLoaderInterface;
use TechDivision\Import\Observers\DynamicAttributeObserverInterface;
use TechDivision\Import\Product\Utils\RelationTypes;
use TechDivision\Import\Product\Variant\Utils\ColumnKeys;
use TechDivision\Import\Product\Variant\Utils\MemberNames;
use TechDivision\Import\Product\Variant\Utils\EntityTypeCodes;
use TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface;
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;
use Doctrine\Common\Collections\Collection;

/**
 * Oberserver that provides functionality for the product variant super attributes replace operation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class VariantSuperAttributeObserver extends AbstractProductImportObserver implements DynamicAttributeObserverInterface
{

    /**
     * The ID of the actual store to use.
     *
     * @var integer
     */
    protected $storeId;

    /**
     * The EAV attribute to handle.
     *
     * @var array
     */
    protected $eavAttribute;

    /**
     * The tempoarary stored product super attribute ID.
     *
     * @var integer
     */
    protected $productSuperAttributeId;

    /**
     * The product variant processor instance.
     *
     * @var \TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface
     */
    protected $productVariantProcessor;

    /**
     * The attribute loader instance.
     *
     * @var \TechDivision\Import\Observers\AttributeLoaderInterface
     */
    protected $attributeLoader;

    /**
     * The collection with entity merger instances.
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $entityMergers;

    /**
     * Initialize the "dymanmic" columns.
     *
     * @var array
     */
    protected $columns = array(
        EntityTypeCodes::CATALOG_PRODUCT_SUPER_ATTRIBUTE => array(
            MemberNames::POSITION    => array(ColumnKeys::VARIANT_VARIATION_POSITION, BackendTypeKeys::BACKEND_TYPE_INT)
        ),
        EntityTypeCodes::CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL => array(
            MemberNames::VALUE       => array(ColumnKeys::VARIANT_VARIATION_LABEL, BackendTypeKeys::BACKEND_TYPE_VARCHAR),
            MemberNames::USE_DEFAULT => array(ColumnKeys::VARIANT_VARIATION_USE_DEFAULT, BackendTypeKeys::BACKEND_TYPE_INT)
        )
    );

    /**
     * Initialize the observer with the passed product variant processor instance.
     *
     * @param \TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface $productVariantProcessor The product variant processor instance
     * @param \TechDivision\Import\Observers\AttributeLoaderInterface|null                   $attributeLoader         The attribute loader instance
     * @param \Doctrine\Common\Collections\Collection|null                                   $entityMergers           The collection with the entity merger instances
     * @param \TechDivision\Import\Observers\StateDetectorInterface|null                     $stateDetector           The state detector instance
     */
    public function __construct(
        ProductVariantProcessorInterface $productVariantProcessor,
        AttributeLoaderInterface $attributeLoader = null,
        Collection $entityMergers = null,
        StateDetectorInterface $stateDetector = null
    ) {

        // initialize the product variant processor and the attribute loader instance
        $this->productVariantProcessor = $productVariantProcessor;
        $this->attributeLoader = $attributeLoader;
        $this->entityMergers = $entityMergers;


        // pass the state detector to the parent method
        parent::__construct($stateDetector);
    }

    /**
     * Return's the product variant processor instance.
     *
     * @return \TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface The product variant processor instance
     */
    protected function getProductVariantProcessor()
    {
        return $this->productVariantProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {
        // extract the parent SKU and attribute code from the row
        $parentSku = $this->getValue(ColumnKeys::VARIANT_PARENT_SKU);
        $attributeCode = $this->getValue(ColumnKeys::VARIANT_ATTRIBUTE_CODE);

        // query whether or not the super attribute has already been processed
        if ($this->hasBeenProcessedRelation($parentSku, $attributeCode, RelationTypes::VARIANT_SUPER_ATTRIBUTE)) {
            return;
        }

        // prepare the store view code
        $this->prepareStoreViewCode($this->getRow());

        // preserve the parent ID
        $this->setParentId($this->mapParentSku($parentSku));

        try {
            // load the EAV attribute with the found attribute code
            $this->setEavAttribute($this->getEavAttributeByAttributeCode($attributeCode));
        } catch (\Exception $e) {
            // extract the child SKU and attribute set code from the row
            $childSku= $this->getValue(ColumnKeys::VARIANT_CHILD_SKU);
            $attributeSetCode = $this->getValue(ColumnKeys::ATTRIBUTE_SET_CODE);
            // prepare a more detailed error message
            $message = $this->appendExceptionSuffix(
                sprintf(
                    'Can\'t find attribute code "%s" in attribut set "%s" for variant SKU "%s" to create simple SKU "%s"',
                    $attributeCode,
                    $attributeSetCode,
                    $parentSku,
                    $childSku
                )
            );

            // if we're NOT in debug mode, re-throw a more detailed exception
            $wrappedException = $this->wrapException(
                array(ColumnKeys::VARIANT_ATTRIBUTE_CODE),
                new \Exception($message, 0, $e)
            );

            // Query whether strict mode is disabled
            if (!$this->isStrictMode()) {
                // log a warning and return immediately
                $this->getSystemLogger()->warning($wrappedException->getMessage());
                $this->mergeStatus(
                    array(
                        RegistryKeys::NO_STRICT_VALIDATIONS => array(
                            basename($this->getFilename()) => array(
                                $this->getLineNumber() => array(
                                    ColumnKeys::VARIANT_ATTRIBUTE_CODE =>  $wrappedException->getMessage()
                                )
                            )
                        )
                    )
                );
                return;
            }

            // else, throw the exception
            throw $wrappedException;
        }

        try {
            // initialize and save the super attribute
            $attr = $this->prepareDynamicAttributes(EntityTypeCodes::CATALOG_PRODUCT_SUPER_ATTRIBUTE, $this->prepareProductSuperAttributeAttributes());
            if ($this->hasChanges($productSuperAttribute = $this->initializeProductSuperAttribute($attr))) {
                $this->persistProductSuperAttribute($productSuperAttribute);
            }

            // initialize and save the super attribute label
            $attr = $this->prepareDynamicAttributes(EntityTypeCodes::CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL, $this->prepareProductSuperAttributeLabelAttributes());
            if ($this->hasChanges($productSuperAttributeLabel = $this->initializeProductSuperAttributeLabel($attr))) {
                $this->persistProductSuperAttributeLabel($productSuperAttributeLabel);
            }

            // mark the super attribute as processed
            $this->addProcessedRelation($parentSku, $attributeCode, RelationTypes::VARIANT_SUPER_ATTRIBUTE);
        } catch (\Exception $e) {
            // prepare a more detailed error message
            $message = $this->appendExceptionSuffix(
                sprintf(
                    'Super attribute for SKU %s and attribute %s can\'t be created',
                    $parentSku,
                    $attributeCode
                )
            );

            // if we're NOT in debug mode, re-throw a more detailed exception
            $wrappedException = $this->wrapException(
                array(ColumnKeys::VARIANT_PARENT_SKU, ColumnKeys::VARIANT_ATTRIBUTE_CODE),
                new \Exception($message, 0, $e)
            );

            // query whether or not, debug mode is enabled
            if (!$this->isStrictMode()) {
                // log a warning and return immediately
                $this->getSystemLogger()->warning($wrappedException->getMessage());
                $this->mergeStatus(
                    array(
                        RegistryKeys::NO_STRICT_VALIDATIONS => array(
                            basename($this->getFilename()) => array(
                                $this->getLineNumber() => array(
                                    ColumnKeys::VARIANT_PARENT_SKU =>  $wrappedException->getMessage()
                                )
                            )
                        )
                    )
                );
                return;
            }

            // else, throw the exception
            throw $wrappedException;
        }
    }

    /**
     * Merge's and return's the entity with the passed attributes and set's the
     * passed status.
     *
     * @param array       $entity         The entity to merge the attributes into
     * @param array       $attr           The attributes to be merged
     * @param string|null $changeSetName  The change set name to use
     * @param string|null $entityTypeCode The entity type code to use
     *
     * @return array The merged entity
     * @todo https://github.com/techdivision/import/issues/179
     */
    protected function mergeEntity(array $entity, array $attr, $changeSetName = null, $entityTypeCode = null)
    {
        return array_merge(
            $entity,
            ($this->entityMergers && $this->entityMergers->containsKey($entityTypeCode)) ? $this->entityMergers->get($entityTypeCode)->merge($this, $entity, $attr) : $attr,
            array(EntityStatus::MEMBER_NAME => $this->detectState($entity, $attr, $changeSetName))
        );
    }

    /**
     * Appends the dynamic attributes to the static ones and returns them.
     *
     * @param string $entityTypeCode   The entity type code load to append the dynamic attributes for
     * @param array  $staticAttributes The array with the static attributes to append the dynamic to
     *
     * @return array The array with all available attributes
     */
    protected function prepareDynamicAttributes(string $entityTypeCode, array $staticAttributes) : array
    {
        return array_merge(
            $staticAttributes,
            $this->attributeLoader ? $this->attributeLoader->load($this, $this->columns[$entityTypeCode]) : array()
        );
    }

    /**
     * Prepare the product super attribute attributes that has to be persisted.
     *
     * @return array The prepared product attribute attributes
     */
    protected function prepareProductSuperAttributeAttributes()
    {
        // load the parent ID
        $parentId = $this->getParentId();

        // load the attribute ID and position
        $attributeId = $this->getAttributeId();

        // initialize the attributes and return them
        return $this->initializeEntity(
            $this->loadRawEntity(
                EntityTypeCodes::CATALOG_PRODUCT_SUPER_ATTRIBUTE,
                array(
                    MemberNames::PRODUCT_ID   => $parentId,
                    MemberNames::ATTRIBUTE_ID => $attributeId
                )
            )
        );
    }

    /**
     * Prepare the product super attribute label attributes that has to be persisted.
     *
     * @return array The prepared product super attribute label attributes
     */
    protected function prepareProductSuperAttributeLabelAttributes()
    {

        // extract the parent/child ID as well as option value and variation label from the row
        $label = $this->getValue(ColumnKeys::VARIANT_VARIATION_LABEL);
        $useDefault = $this->getValue(ColumnKeys::VARIANT_VARIATION_USE_DEFAULT, 0);

        // query whether or not we've to create super attribute labels
        if (empty($label)) {
            $label = $this->getFrontendLabel();
        }

        // initialize the attributes and return them
        return $this->initializeEntity(
            $this->loadRawEntity(
                EntityTypeCodes::CATALOG_PRODUCT_SUPER_ATTRIBUTE_LABEL,
                array(
                    MemberNames::PRODUCT_SUPER_ATTRIBUTE_ID => $this->getProductSuperAttributeId(),
                    MemberNames::STORE_ID                   => $this->getRowStoreId(StoreViewCodes::ADMIN),
                    MemberNames::USE_DEFAULT                => $useDefault,
                    MemberNames::VALUE                      => $label
                )
            )
        );
    }

    /**
     * Load's and return's a raw entity without primary key but the mandatory members only and nulled values.
     *
     * @param string $entityTypeCode The entity type code to return the raw entity for
     * @param array  $data           An array with data that will be used to initialize the raw entity with
     *
     * @return array The initialized entity
     */
    protected function loadRawEntity($entityTypeCode, array $data = array())
    {
        return $this->getProductVariantProcessor()->loadRawEntity($entityTypeCode, $data);
    }

    /**
     * Initialize the product super attribute with the passed attributes and returns an instance.
     *
     * @param array $attr The product super attribute attributes
     *
     * @return array The initialized product super attribute
     */
    protected function initializeProductSuperAttribute(array $attr)
    {
        return $attr;
    }

    /**
     * Initialize the product super attribute label with the passed attributes and returns an instance.
     *
     * @param array $attr The product super attribute label attributes
     *
     * @return array The initialized product super attribute label
     */
    protected function initializeProductSuperAttributeLabel(array $attr)
    {
        return $attr;
    }

    /**
     * Set's the actual EAV attribute.
     *
     * @param array $eavAttribute The actual EAV attribute
     *
     * @return void
     */
    protected function setEavAttribute(array $eavAttribute)
    {
        $this->eavAttribute = $eavAttribute;
    }

    /**
     * Return's the actual EAV attribute.
     *
     * @return array The actual EAV attribute
     */
    protected function getEavAttribute()
    {
        return $this->eavAttribute;
    }

    /**
     * Return's the frontend label from the actual EAV attribute.
     *
     * @return string The frontend label
     */
    protected function getFrontendLabel()
    {
        return $this->eavAttribute[MemberNames::FRONTENT_LABEL];
    }

    /**
     * Return's the attribute ID from the actual EAV attribute.
     *
     * @return integer The attribute ID
     */
    protected function getAttributeId()
    {
        return $this->eavAttribute[MemberNames::ATTRIBUTE_ID];
    }

    /**
     * Set's the actual product super attribute ID.
     *
     * @param integer $productSuperAttributeId The product super attribute ID
     *
     * @return void
     */
    protected function setProductSuperAttributeId($productSuperAttributeId)
    {
        $this->productSuperAttributeId = $productSuperAttributeId;
    }

    /**
     * Return's the product super attribute ID.
     *
     * @return integer The product super attribute ID
     */
    protected function getProductSuperAttributeId()
    {
        return $this->productSuperAttributeId;
    }

    /**
     * Map's the passed SKU of the parent product to it's PK.
     *
     * @param string $parentSku The SKU of the parent product
     *
     * @return integer The primary key used to create relations
     */
    protected function mapParentSku($parentSku)
    {
        return $this->mapSkuToEntityId($parentSku);
    }

    /**
     * Return the entity ID for the passed SKU.
     *
     * @param string $sku The SKU to return the entity ID for
     *
     * @return integer The mapped entity ID
     * @throws \Exception Is thrown if the SKU is not mapped yet
     */
    protected function mapSkuToEntityId($sku)
    {
        return $this->getSubject()->mapSkuToEntityId($sku);
    }

    /**
     * Set's the ID of the parent product to relate the variant with.
     *
     * @param integer $parentId The ID of the parent product
     *
     * @return void
     */
    protected function setParentId($parentId)
    {
        $this->getSubject()->setParentId($parentId);
    }

    /**
     * Return's the ID of the parent product to relate the variant with.
     *
     * @return integer The ID of the parent product
     */
    protected function getParentId()
    {
        return $this->getSubject()->getParentId();
    }

    /**
     * Return's the store for the passed store code.
     *
     * @param string $storeCode The store code to return the store for
     *
     * @return array The requested store
     * @throws \Exception Is thrown, if the requested store is not available
     */
    protected function getStoreByStoreCode($storeCode)
    {
        return $this->getSubject()->getStoreByStoreCode($storeCode);
    }

    /**
     * Return's an array with the available stores.
     *
     * @return array The available stores
     */
    protected function getStores()
    {
        return $this->getSubject()->getStores();
    }

    /**
     * Return's the first EAV attribute for the passed attribute code.
     *
     * @param string $attributeCode The attribute code
     *
     * @return array The array with the EAV attribute
     */
    protected function getEavAttributeByAttributeCode($attributeCode)
    {
        return $this->getSubject()->getEavAttributeByAttributeCode($attributeCode);
    }

    /**
     * Persist's the passed product super attribute data and return's the ID.
     *
     * @param array $productSuperAttribute The product super attribute data to persist
     *
     * @return void
     */
    protected function persistProductSuperAttribute($productSuperAttribute)
    {
        $this->setProductSuperAttributeId($this->getProductVariantProcessor()->persistProductSuperAttribute($productSuperAttribute));
    }

    /**
     * Persist's the passed product super attribute label data and return's the ID.
     *
     * @param array $productSuperAttributeLabel The product super attribute label data to persist
     *
     * @return void
     */
    protected function persistProductSuperAttributeLabel($productSuperAttributeLabel)
    {
        return $this->getProductVariantProcessor()->persistProductSuperAttributeLabel($productSuperAttributeLabel);
    }
}
