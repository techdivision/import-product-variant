<?php

/**
 * TechDivision\Import\Product\Variant\Utils\SqlStatements
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

namespace TechDivision\Import\Product\Variant\Utils;

/**
 * Utility class with the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class SqlStatements extends \TechDivision\Import\Product\Utils\SqlStatements
{

    /**
     * The SQL statement to load an existing product relation with the passed parent/child ID.
     *
     * @var string
     */
    const PRODUCT_RELATION = 'SELECT *
                                FROM catalog_product_relation
                               WHERE parent_id = :parent_id
                                 AND child_id = :child_id';

    /**
     * The SQL statement to load an existing product super link with the passed prodcut/parent ID.
     *
     * @var string
     */
    const PRODUCT_SUPER_LINK = 'SELECT *
                                  FROM catalog_product_super_link
                                 WHERE product_id = :product_id
                                   AND parent_id = :parent_id';

    /**
     * The SQL statement to load an existing product super attribute with the passed product/attribute ID.
     *
     * @var string
     */
    const PRODUCT_SUPER_ATTRIBUTE = 'SELECT *
                                       FROM catalog_product_super_attribute
                                      WHERE product_id = :product_id
                                        AND attribute_id = :attribute_id';

    /**
     * The SQL statement to load an existing product super attribute label with the passed product super attribute/store ID.
     *
     * @var string
     */
    const PRODUCT_SUPER_ATTRIBUTE_LABEL = 'SELECT *
                                             FROM catalog_product_super_attribute_label
                                            WHERE product_super_attribute_id = :product_super_attribute_id
                                              AND store_id = :store_id';

    /**
     * The SQL statement to create a new product relation.
     *
     * @var string
     */
    const CREATE_PRODUCT_RELATION = 'INSERT
                                       INTO catalog_product_relation (
                                                parent_id,
                                                child_id
                                            )
                                     VALUES (:parent_id,
                                             :child_id)';

    /**
     * The SQL statement to create a new product super link.
     *
     * @var string
     */
    const CREATE_PRODUCT_SUPER_LINK = 'INSERT
                                         INTO catalog_product_super_link (
                                                  product_id,
                                                  parent_id
                                              )
                                       VALUES (:product_id,
                                               :parent_id)';

    /**
     * The SQL statement to create a new product super attribute.
     *
     * @var string
     */
    const CREATE_PRODUCT_SUPER_ATTRIBUTE = 'INSERT
                                              INTO catalog_product_super_attribute (
                                                       product_id,
                                                       attribute_id,
                                                       position
                                                   )
                                            VALUES (:product_id,
                                                    :attribute_id,
                                                    :position)';

    /**
     * The SQL statement to update an existing product super attribute.
     *
     * @var string
     */
    const UPDATE_PRODUCT_SUPER_ATTRIBUTE = 'UPDATE catalog_product_super_attribute
                                               SET product_id = :product_id,
                                                   attribute_id = :attribute_id,
                                                   position = :position
                                             WHERE product_super_attribute_id = :product_super_attribute_id';

    /**
     * The SQL statement to create a new product super attribute label.
     *
     * @var string
     */
    const CREATE_PRODUCT_SUPER_ATTRIBUTE_LABEL = 'INSERT
                                                    INTO catalog_product_super_attribute_label (
                                                             product_super_attribute_id,
                                                             store_id,
                                                             use_default,
                                                             value
                                                         )
                                                  VALUES (:product_super_attribute_id,
                                                          :store_id,
                                                          :use_default,
                                                          :value)';

    /**
     * The SQL statement to update an existing product super attribute label.
     *
     * @var string
     */
    const UPDATE_PRODUCT_SUPER_ATTRIBUTE_LABEL = 'UPDATE catalog_product_super_attribute_label
                                                     SET product_super_attribute_id = :product_super_attribute_id,
                                                         store_id = :store_id,
                                                         use_default = :use_default,
                                                         value = :value
                                                   WHERE value_id = :value_id';
}
