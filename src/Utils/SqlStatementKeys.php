<?php

/**
 * TechDivision\Import\Product\Variant\Utils\SqlStatementKeys
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Utils;

/**
 * Utility class with the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class SqlStatementKeys extends \TechDivision\Import\Product\Utils\SqlStatementKeys
{

    /**
     * The SQL statement to load an existing product super link with the passed prodcut/parent ID.
     *
     * @var string
     */
    const PRODUCT_SUPER_LINK = 'product_super_link';

    /**
     * The SQL statement to load all existing product super link with the passed parent ID.
     *
     * @var string
     */
    const PRODUCT_SUPER_LINK_PARENT = 'product_super_link_parent';

    /**
     * The SQL statement to load an existing product super attribute with the passed product/attribute ID.
     *
     * @var string
     */
    const PRODUCT_SUPER_ATTRIBUTE = 'product_super_attribute';

    /**
     * The SQL statement to load an existing product super attributes with the passed product ID.
     *
     * @var string
     */
    const PRODUCT_SUPER_ATTRIBUTE_BY_PRODUCT = 'product_super_attribute_by_product';

    /**
     * The SQL statement to load an existing product super attribute label with the passed product super attribute/store ID.
     *
     * @var string
     */
    const PRODUCT_SUPER_ATTRIBUTE_LABEL = 'product_super_attribute_label';

    /**
     * The SQL statement to create a new product super link.
     *
     * @var string
     */
    const CREATE_PRODUCT_SUPER_LINK = 'create.product_super_link';

    /**
     * The SQL statement to create a new product super attribute.
     *
     * @var string
     */
    const CREATE_PRODUCT_SUPER_ATTRIBUTE = 'create.product_super_attribute';

    /**
     * The SQL statement to update an existing product super attribute.
     *
     * @var string
     */
    const UPDATE_PRODUCT_SUPER_ATTRIBUTE = 'update.product_super_attribute';

    /**
     * The SQL statement to update an existing product super attribute.
     *
     * @var string
     */
    const DELETE_PRODUCT_SUPER_ATTRIBUTE = 'delete.product_super_attribute';

    /**
     * The SQL statement to create a new product super attribute label.
     *
     * @var string
     */
    const CREATE_PRODUCT_SUPER_ATTRIBUTE_LABEL = 'create.product_super_attribute_label';

    /**
     * The SQL statement to update an existing product super attribute label.
     *
     * @var string
     */
    const UPDATE_PRODUCT_SUPER_ATTRIBUTE_LABEL = 'update.product_super_attribute_label';

    /**
     * The SQL statement to delete an existing product super link.
     *
     * @var string
     */
    const DELETE_PRODUCT_SUPER_LINK = 'delete.product_super_link';
}
