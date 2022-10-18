<?php

/**
 * TechDivision\Import\Product\Variant\Observers\VariantSuperLinkObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Observers;

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Product\Utils\RelationTypes;
use TechDivision\Import\Product\Variant\Utils\ColumnKeys;
use TechDivision\Import\Product\Variant\Utils\MemberNames;
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;
use TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface;

/**
 * Oberserver that provides functionality for the product variant super link replace operation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class VariantSuperLinkObserver extends AbstractProductImportObserver
{

    /**
     * The product variant processor instance.
     *
     * @var \TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface
     */
    protected $productVariantProcessor;

    /**
     * Initialize the observer with the passed product variant processor instance.
     *
     * @param \TechDivision\Import\Product\Variant\Services\ProductVariantProcessorInterface $productVariantProcessor The product variant processor instance
     */
    public function __construct(ProductVariantProcessorInterface $productVariantProcessor)
    {
        $this->productVariantProcessor = $productVariantProcessor;
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

        // load the parent/child SKUs
        $parentSku = $this->getValue(ColumnKeys::VARIANT_PARENT_SKU);
        $childSku = $this->getValue(ColumnKeys::VARIANT_CHILD_SKU);

        // query whether or not the super link has already been processed
        if ($this->hasBeenProcessedRelation($parentSku, $childSku, RelationTypes::VARIANT_SUPER_LINK)) {
            return;
        }

        try {
            // try to load and map the parent ID
            $this->parentId = $this->mapSku($parentSku);
        } catch (\Exception $e) {
            if (!$this->getSubject()->isStrictMode()) {
                $this->getSubject()
                    ->getSystemLogger()
                    ->warning($this->getSubject()->appendExceptionSuffix($e->getMessage()));
                $this->mergeStatus(
                    array(
                        RegistryKeys::NO_STRICT_VALIDATIONS => array(
                            basename($this->getFilename()) => array(
                                $this->getLineNumber() => array(
                                    ColumnKeys::VARIANT_PARENT_SKU => $e->getMessage()
                                )
                            )
                        )
                    )
                );
                $this->skipRow();
                return;
            } else {
                throw $this->wrapException(array(ColumnKeys::VARIANT_PARENT_SKU), $e);
            }
        }

        try {
            // try to load and map the child ID
            $this->childId = $this->mapChildSku($childSku);
        } catch (\Exception $e) {
            $this->getSubject()
                ->getSystemLogger()
                ->warning($this->getSubject()->appendExceptionSuffix($e->getMessage()));
            if (!$this->getSubject()->isStrictMode()) {
                $this->mergeStatus(
                    array(
                        RegistryKeys::NO_STRICT_VALIDATIONS => array(
                            basename($this->getFilename()) => array(
                                $this->getLineNumber() => array(
                                    ColumnKeys::VARIANT_CHILD_SKU =>  $e->getMessage()
                                )
                            )
                        )
                    )
                );
                $this->skipRow();
                return;
            } else {
                throw $this->wrapException(array(ColumnKeys::VARIANT_CHILD_SKU), $e);
            }
        }

        try {
            // prepare and persist the product super link
            if ($productSuperLink = $this->initializeProductSuperLink($this->prepareProductSuperLinkAttributes())) {
                $this->persistProductSuperLink($productSuperLink);
            }

            // mark the super link as processed
            $this->addProcessedRelation($parentSku, $childSku, RelationTypes::VARIANT_SUPER_LINK);
        } catch (\Exception $e) {
            // prepare a more detailed error message
            $message = $this->appendExceptionSuffix(
                sprintf(
                    'Super link with SKUs %s => %s can\'t be created',
                    $parentSku,
                    $childSku
                )
            );

            // if we're NOT in debug mode, re-throw a more detailed exception
            $wrappedException = $this->wrapException(
                array(ColumnKeys::VARIANT_PARENT_SKU, ColumnKeys::VARIANT_CHILD_SKU),
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
            // else, throw the exception is strict mode on
            throw $wrappedException;
        }
    }

    /**
     * Prepare the product super link attributes that has to be persisted.
     *
     * @return array The prepared product super link attributes
     */
    protected function prepareProductSuperLinkAttributes()
    {

        // initialize and return the entity
        return $this->initializeEntity(
            array(
                MemberNames::PRODUCT_ID => $this->childId,
                MemberNames::PARENT_ID  => $this->parentId
            )
        );
    }

    /**
     * Initialize the product super link with the passed attributes and returns an instance.
     *
     * @param array $attr The product super link attributes
     *
     * @return array|null The initialized product super link, or null if the super link already exsist
     */
    protected function initializeProductSuperLink(array $attr)
    {
        return $attr;
    }

    /**
     * Return the entity ID for the passed SKU.
     *
     * @param string $sku The SKU to return the entity ID for
     *
     * @return integer The mapped entity ID
     * @throws \Exception Is thrown if the SKU is not mapped yet
     */
    protected function mapSku($sku)
    {
        return $this->getSubject()->mapSkuToEntityId($sku);
    }

    /**
     * Return the entity ID for the passed child SKU.
     *
     * @param string $sku The SKU to return the entity ID for
     *
     * @return integer The mapped entity ID
     * @throws \Exception Is thrown if the SKU is not mapped yet
     */
    protected function mapChildSku($sku)
    {
        return $this->getSubject()->mapSkuToEntityId($sku);
    }

    /**
     * Persist's the passed product super link data and return's the ID.
     *
     * @param array $productSuperLink The product super link data to persist
     *
     * @return void
     */
    protected function persistProductSuperLink($productSuperLink)
    {
        return $this->getProductVariantProcessor()->persistProductSuperLink($productSuperLink);
    }
}
