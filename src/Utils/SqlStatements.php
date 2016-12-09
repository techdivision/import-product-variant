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

use TechDivision\Import\Product\Utils\SqlStatements as FallbackStatements;

/**
 * A SSB providing process registry functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class SqlStatements extends FallbackStatements
{

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

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
                                     VALUES (?, ?)';

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
                                       VALUES (?, ?)';

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
                                            VALUES (?, ?, ?)';

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
                                                  VALUES (?, ?, ?, ?)';
}
