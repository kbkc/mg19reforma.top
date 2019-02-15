<?php
require_once 'excelParser.php';

/**
 * Class Itdelight_Shell_ImportQtyFromExcel
 */
class Itdelight_Shell_ImportQtyFromExcel extends Itdelight_Shell_ExcelParser
{
    /**
     * @var array
     */
    protected $_argname = array();

    /**
     * Itdelight_Shell_ImportQtyFromExcel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Time limit to infinity
        set_time_limit(0);

        if ($this->getArg('file')) {
            $this->_argname = array_merge(
                $this->_argname,
                array_map(
                    'trim',
                    explode(',', $this->getArg('file'))
                )
            );
        } else {
            throw new Mage_Core_Exception('Argument --file is required.');
        }
    }

    // Shell script point of entry
    /**
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function run()
    {
        try {
            $excelData = $this->parseXML($this->getArg('file'));
            $productQtyExcel = $excelData[0]->toArray();
            $countProductQty = count($productQtyExcel);

            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            for ($i = 1; $i < $countProductQty; $i++) {
                /** @var Mage_Catalog_Model_Product $product */
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $productQtyExcel[$i][0]);
                if ($product) {
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                    $stockItem->setQty($productQtyExcel[$i][1]);
                    $stockItem->save();
                }
            }
            
            Mage::log('import finished ', Zend_Log::INFO, 'importQtyFromExcel.log', true);

        } catch (Mage_Core_Exception $e) {
            throw new Mage_Core_Exception($e->getMessage());
        }

    }


    /**
     * Usage instructions
     * @return string
     */
    public function usageHelp()
    {
        return <<<USAGE
        imports product qty from xlsx
Usage:  php -f importQtyFromExcel.php -- [options]
 
  --file <filepath>       path to csv file
 
  help                   This help
 
USAGE;
    }
}

// Instantiate
$shell = new Itdelight_Shell_ImportQtyFromExcel();

// Initiate script
$shell->run();