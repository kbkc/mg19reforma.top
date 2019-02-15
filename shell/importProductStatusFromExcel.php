<?php
require_once 'excelParser.php';

/**
 * Class Itdelight_Shell_ImportProductStatusFromExcel
 */
class Itdelight_Shell_ImportProductStatusFromExcel extends Itdelight_Shell_ExcelParser
{
    /**
     * @var array
     */
    protected $_argname = array();

    /**
     * Itdelight_Shell_ImportProductStatusFromExcel constructor.
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
            $productStatusExcel = $excelData[0]->toArray();
            $countProductStatus = count($productStatusExcel);

            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            for ($i = 1; $i < $countProductStatus; $i++) {
                /** @var Mage_Catalog_Model_Product $product */
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $productStatusExcel[$i][0]);
                if ($product) {
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                    $stockItem->setQty($productStatusExcel[$i][1]);
                    $stockItem->save();
                    if ($productStatusExcel[$i][2] <= 0) {
                        $productStatusExcel[$i][2] = 2;
                        Mage::getModel('catalog/product_website')->removeProducts(array(1), array($product->getId()));
                        if ($product->hasData('creareseo_discontinued')) {
                            $product->setCreareseoDiscontinued(null);
                        }
                    }
                    $product->setStatus($productStatusExcel[$i][2]);
                    $product->save();
                }
            }
            Mage::log('reindex Proceeded ', Zend_Log::INFO, 'productStatusFinish.log', true);

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
        imports product status (qty & is enabled) from xlsx
Usage:  php -f importProductStatusFromExcel.php -- [options]
 
  --file <filepath>       path to csv file
 
  help                   This help
 
USAGE;
    }
}

// Instantiate
$shell = new Itdelight_Shell_ImportProductStatusFromExcel();

// Initiate script
$shell->run();