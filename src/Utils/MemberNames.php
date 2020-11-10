<?php

/**
 * TechDivision\Import\Product\Variant\Utils\MemberNames
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
 * @link      https://github.com/techdivision/import-product-variation
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Utils;

/**
 * Utility class containing the entities member names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variation
 * @link      http://www.techdivision.com
 */
class MemberNames extends \TechDivision\Import\Product\Utils\MemberNames
{

    /**
     * Name for the member 'parent_id'.
     *
     * @var string
     */
    const PARENT_ID = 'parent_id';

    /**
     * Name for the member 'product_id'.
     *
     * @var string
     */
    const PRODUCT_ID = 'product_id';

    /**
     * Name for the member 'link_id'.
     *
     * @var string
     */
    const LINK_ID = 'link_id';

    /**
     * Name for the member 'child_id'.
     *
     * @var string
     */
    const CHILD_ID = 'child_id';

    /**
     * Name for the member 'product_super_attribute_id'.
     *
     * @var string
     */
    const PRODUCT_SUPER_ATTRIBUTE_ID = 'product_super_attribute_id';

    /**
     * Name for the member 'use_default'.
     *
     * @var string
     */
    const USE_DEFAULT = 'use_default';
}
