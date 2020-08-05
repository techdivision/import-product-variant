<?php

/**
 * TechDivision\Import\Product\Variant\Subjects\VariantSubjectTrait
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

namespace TechDivision\Import\Product\Variant\Subjects;

/**
 * A trait implementation that provides functionality to handle the variant import on subject level.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
trait VariantSubjectTrait
{

    /**
     * The ID of the parent product to relate the variant with.
     *
     * @var integer
     */
    protected $parentId;

    /**
     * Set's the ID of the parent product to relate the variant with.
     *
     * @param integer $parentId The ID of the parent product
     *
     * @return void
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Return's the ID of the parent product to relate the variant with.
     *
     * @return integer The ID of the parent product
     */
    public function getParentId()
    {
        return $this->parentId;
    }
}
