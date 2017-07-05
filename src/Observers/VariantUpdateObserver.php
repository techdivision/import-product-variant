<?php

/**
 * TechDivision\Import\Product\Variant\Observers\VariantUpdateObserver
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

namespace TechDivision\Import\Product\Variant\Observers;

use TechDivision\Import\Product\Variant\Utils\MemberNames;

/**
 * Oberserver that provides functionality for the product variant add/update operation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class VariantUpdateObserver extends VariantObserver
{

    /**
     * Initialize the product relation with the passed attributes and returns an instance.
     *
     * @param array $attr The product relation attributes
     *
     * @return array|null The initialized product relation, or null if the relation already exsist
     */
    protected function initializeProductRelation(array $attr)
    {

        // laod child/parent ID
        $childId = $attr[MemberNames::CHILD_ID];
        $parentId = $attr[MemberNames::PARENT_ID];

        // query whether or not the product relation already exists
        if ($this->loadProductRelation($parentId, $childId)) {
            return;
        }

        // simply return the attributes
        return $attr;
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

        // laod parent/product ID
        $parentId = $attr[MemberNames::PARENT_ID];
        $productId = $attr[MemberNames::PRODUCT_ID];

        // query whether or not the product super link already exists
        if ($this->loadProductSuperLink($productId, $parentId)) {
            return;
        }

        // simply return the attributes
        return $attr;
    }

    /**
     * Load's the product relation with the passed parent/child ID.
     *
     * @param integer $parentId The entity ID of the product relation's parent product
     * @param integer $childId  The entity ID of the product relation's child product
     *
     * @return array The product relation
     */
    protected function loadProductRelation($parentId, $childId)
    {
        return $this->getProductVariantProcessor()->loadProductRelation($parentId, $childId);
    }

    /**
     * Load's the product super link with the passed product/parent ID.
     *
     * @param integer $productId The entity ID of the product super link's product
     * @param integer $parentId  The entity ID of the product super link's parent product
     *
     * @return array The product super link
     */
    protected function loadProductSuperLink($productId, $parentId)
    {
        return $this->getProductVariantProcessor()->loadProductSuperLink($productId, $parentId);
    }
}
