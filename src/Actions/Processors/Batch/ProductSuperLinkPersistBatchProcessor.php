<?php

/**
 * TechDivision\Import\Product\Variant\Actions\Processors\Batch\ProductSuperLinkPersistBatchProcessor
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

namespace TechDivision\Import\Product\Variant\Actions\Processors\Batch;

use TechDivision\Import\Actions\Processors\Batch\AbstractPersistBatchProcessor;

/**
 * The product super link persist batch processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class ProductSuperLinkPersistBatchProcessor extends AbstractPersistBatchProcessor
{

    /**
     * {@inheritDoc}
     * @see \TechDivision\Import\Product\Variant\Actions\Processors\Batch\AbstractPersistBatchProcessor::getNumberOfPlaceholders()
     */
    protected function getNumberOfPlaceholders()
    {
        return 2;
    }

    /**
     * {@inheritDoc}
     * @see \TechDivision\Import\Product\Variant\Actions\Processors\Batch\AbstractPersistBatchProcessor::getStatement()
     */
    protected function getStatement()
    {
        $utilityClassName = $this->getUtilityClassName();
        return $utilityClassName::CREATE_PRODUCT_SUPER_LINK;
    }
}
