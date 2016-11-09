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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Product\Variant\Utils;

/**
 * A SSB providing process registry functionality.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class SqlStatements
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
     * Return's the Magento edition/version specific utility class containing
     * the SQL statements to use.
     *
     * @param string $magentoEdition The Magento edition to use, EE or CE
     * @param string $magentoVersion The Magento version to use, e. g. 2.1.0
     *
     * @return string The fully qualified utility class name
     */
    public static function getUtilityClassName($magentoEdition, $magentoVersion)
    {

        // prepare the Magento edition/version specific utility classname
        $utilClassName = sprintf('TechDivision\Import\Product\Variant\Utils\%s\V%s\SqlStatements', ucfirst($magentoEdition), $magentoVersion);

        // if NOT available, use the default utility class name
        if (!class_exists($utilClassName)) {
            // prepare the Magento edition/version specific utility classname
            if (!class_exists($utilClassName = sprintf('TechDivision\Import\Product\Variant\Utils\%s\SqlStatements', ucfirst($magentoEdition)))) {
                $utilClassName = __CLASS__;
            }
        }

        // return the utility class name
        return $utilClassName;
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