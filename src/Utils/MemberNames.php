<?php

/**
 * TechDivision\Import\Product\Variant\Utils\MemberNames
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variation
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Utils;

/**
 * Utility class containing the entities member names.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
