<?php

/**
 * TechDivision\Import\Product\Variant\Subjects\VariantSubject
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Subjects;

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Product\Utils\ConfigurationKeys;
use TechDivision\Import\Product\Subjects\AbstractProductSubject;
use TechDivision\Import\Subjects\CleanUpColumnsSubjectInterface;

/**
 * A subject implementation that handles the process to import product variants.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class VariantSubject extends AbstractProductSubject implements CleanUpColumnsSubjectInterface
{

    /**
     * The trait that provides the functionality to import variants on subject level.
     *
     * @var \TechDivision\Import\Product\Variant\Subjects\VariantSubjectTrait
     */
    use VariantSubjectTrait;

    /**
     * Intializes the previously loaded global data for exactly one variants.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function setUp($serial)
    {

        // invoke parent method
        parent::setUp($serial);

        // load the entity manager and the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // load the status of the actual import process
        $status = $registryProcessor->getAttribute(RegistryKeys::STATUS);

        // load the SKU => entity ID mapping
        $this->skuEntityIdMapping = $status[RegistryKeys::SKU_ENTITY_ID_MAPPING] ?? null;
    }

    /**
     * Merge the columns from the configuration with all image type columns to define which
     * columns should be cleaned-up.
     *
     * @return array The columns that has to be cleaned-up
     */
    public function getCleanUpColumns()
    {

        // initialize the array for the columns that has to be cleaned-up
        $cleanUpColumns = array();

        // query whether or not an array has been specified in the configuration
        if ($this->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS)) {
            $cleanUpColumns = $this->getConfiguration()->getParam(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS);
        }

        // return the array with the column names
        return $cleanUpColumns;
    }
}
