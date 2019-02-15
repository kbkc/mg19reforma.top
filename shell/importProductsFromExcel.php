<?php
require_once 'excelParser.php';

/**
 * Class Itdelight_Shell_ImportProductsFromExcel
 */
class Itdelight_Shell_ImportProductsFromExcel extends Itdelight_Shell_ExcelParser
{
    /**
     * @var array
     */
    protected $_argname = array();

    /**
     * Itdelight_Shell_ImportProductsFromExcel constructor.
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

    protected function cmp($a, $b)
    {
        if ($a[2] == $b[2]) {
            return 0;
        }
        return ($a[2] < $b[2]) ? -1 : 1;
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
            $productsExcel = $excelData[0]->toArray();
            $countProducts = count($productsExcel) - 1;
            $categoriesExcel = $excelData[1]->toArray();
            array_shift($categoriesExcel);
            usort($categoriesExcel, array($this, "cmp"));
            $relationsExcel = $excelData[2]->toArray();
            $relationsCount = count($relationsExcel);

            $countCategories = count($categoriesExcel);
            $rootParentCat = Mage::getModel('catalog/category')->loadByAttribute('url_key', 'shop');
            $ruStoreId = Mage::getModel('core/store')->loadConfig('ru')->getId();

            $attribute_set = Mage::getModel("eav/entity_attribute_set")->getCollection();
            $defaultAttributeSetId = $attribute_set->addFieldToFilter("attribute_set_name", 'default')
                ->addFieldToFilter("entity_type_id",
                    (int)Mage::getModel('eav/entity_type')->loadByCode(Mage_Catalog_Model_Product::ENTITY)->getEntityTypeId()
                )->getFirstItem()->getId();

            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            $catMap = array();

            for ($i = 0; $i < $countCategories; $i++) {
                /** @var Mage_Catalog_Model_Category $category */
                $category = Mage::getModel('catalog/category')->loadByAttribute('url_key', $categoriesExcel[$i][5]);
                if (!$category) {
                    $category = Mage::getModel('catalog/category');
                    $category->setUrlKey($categoriesExcel[$i][5]);
                }

                if ($categoriesExcel[$i][2] == 0) {
                    $parentCategory = $rootParentCat;
                } else {
                    $parentCategory = Mage::getModel('catalog/category')->load($catMap[$categoriesExcel[$i][2]]);
                    if (!$parentCategory) {
                        $parentCategory = $rootParentCat;
                    }
                }

                if ($imgName = $categoriesExcel[$i][1]) {
                    $url = "http://reforma.ua/components/com_jshopping/files/img_categories/$imgName";
                    copy($url, Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category'
                        . DS . $imgName);
                    $category->setImage($imgName);
                }


                $category->setName($categoriesExcel[$i][4]);
                $category->setIsActive(1);
                $category->setIncludeInMenu($categoriesExcel[$i][3]);
                $category->setDisplayMode('PRODUCTS');
                $category->setIsAnchor(0); //for active anchor
                $category->setStoreId(0);
                $category->setDescription(str_replace('_x000D_', '', $categoriesExcel[$i][7]));
                $category->setPath((string)$parentCategory->getPath());

                if ($categoriesExcel[$i][8]) {
                    $category->setMetaTitle($categoriesExcel[$i][8]);
                }
                if ($categoriesExcel[$i][9]) {
                    $category->setMetaDescription($categoriesExcel[$i][9]);
                }
                if ($categoriesExcel[$i][10]) {
                    $category->setMetaKeywords($categoriesExcel[$i][10]);
                }
                $category->save();

                $catMap[$categoriesExcel[$i][0]] = $category->getId();

                unset($category);
            }
            Mage::log('catMap ' . var_export($catMap, true), Zend_Log::ERR, 'catMap.log', true);

            if (!file_exists('media/catalog/product/1/0')) {
                mkdir('media/catalog/product/1/0', 0777, true);
            }
            $productMap = array();
            for ($i = 1; $i <= $countProducts; $i++) {
                /** @var Mage_Catalog_Model_Product $product */
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $productsExcel[$i][1]);
                if (!$product) {//insert new product
                    $product = Mage::getModel('catalog/product');
                    $product->setSku($productsExcel[$i][1]);
                    $product->setStockData(array(
                            'use_config_manage_stock' => 0, //'Use config settings' checkbox
                            'manage_stock' => 1, //manage stock
                            'min_sale_qty' => 0, //Minimum Qty Allowed in Shopping Cart
                            'max_sale_qty' => 10000, //Maximum Qty Allowed in Shopping Cart
                            'is_in_stock' => 1, //Stock Availability
                            'qty' => 999 //qty
                        )
                    );
                }

                if ($imgName = $productsExcel[$i][5]) {
                    $url = "http://reforma.ua/components/com_jshopping/files/img_products/$imgName";
                    if (copy($url, Mage::getBaseDir('media') . DS . 'catalog' . DS
                        . 'product' . DS . '1' . DS . '0' . DS . $imgName)) {
                        $product->setMediaGallery(array('images' => array(), 'values' => array()))//media gallery initialization
                        ->addImageToMediaGallery(Mage::getBaseDir('media') . DS . 'catalog' . DS
                            . 'product' . DS . '1' . DS . '0' . DS . $imgName,
                            array('image', 'thumbnail', 'small_image'), false, false);//assigning image, thumb and small image to media gallery
                    }

                }

                if ($productsExcel[$i][2]) {
                    $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);//catalog and search visibility
                } else {
                    $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG);
                }

                if ($productsExcel[$i][3]) {
                    $product->setPrice($productsExcel[$i][3]);
                }
                if ($productsExcel[$i][4]) {
                    $product->setSpecialPrice($productsExcel[$i][4]);
                }

                if ($productsExcel[$i][9]) {
                    $product->setMetaTitle($productsExcel[$i][9]);
                }
                if ($productsExcel[$i][10]) {
                    $product->setMetaDescription($productsExcel[$i][10]);
                }
                if ($productsExcel[$i][11]) {
                    $product->setMetaKeyword($productsExcel[$i][11]);
                }

                $product
                    ->setWebsiteIds(array(1))//website ID the product is assigned to, as an array
                    ->setAttributeSetId($defaultAttributeSetId)//ID of a attribute set named 'default'
                    ->setTypeId('simple')//product type
                    ->setCreatedAt(strtotime('now'))//product creation time


                    ->setName($productsExcel[$i][6])//product name
                    ->setWeight(1.0000)
                    ->setStatus(1)//product status (1 - enabled, 2 - disabled)
                    ->setTaxClassId(0)//tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                    ->setDescription(str_replace('_x000D_', '', $productsExcel[$i][8]))
                    ->setShortDescription(str_replace('_x000D_', '', $productsExcel[$i][7]));

                $product
                    ->setName($productsExcel[$i][6])
                    ->setDescription(str_replace('_x000D_', '', $productsExcel[$i][8]))
                    ->setShortDescription(str_replace('_x000D_', '', $productsExcel[$i][7]));

                $product->save();

                $productMap[$productsExcel[$i][0]] = $product->getId();
            }
            Mage::log('productMap ' . var_export($productMap, true), Zend_Log::ERR, 'productMap.log', true);


            $resource = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_write');
            for ($i = 1; $i <= $relationsCount; $i++) {
                $searchProduct = Mage::getModel('catalog/product')->load($productMap[$relationsExcel[$i][0]]);
                $searchCat = Mage::getModel('catalog/category')
                    ->load($catMap[$relationsExcel[$i][1]]);

                if ($searchProduct->getId() && $searchCat->getId()) {
                    $checkIfExists = "SELECT `category_id` FROM {$connection->getTableName('catalog_category_product')}"
                        . " WHERE `category_id` = {$searchCat->getId()} AND `product_id` = {$searchProduct->getId()}";
                    $occurrence = $connection->fetchCol($checkIfExists);
                    if (!$occurrence[0] && !($searchProduct->getVisibility() == '2' && $searchCat->getIncludeInMenu())) {
                        $insertRelation = "INSERT INTO {$connection->getTableName('catalog_category_product')} "
                            . "(`category_id`, `product_id`, `position`)"
                            . " VALUES ('{$searchCat->getId()}', '{$searchProduct->getId()}', '{$relationsExcel[$i][2]}')";
                        $connection->query($insertRelation);
                    }
                }

            }


            Mage::log('Relations Proceeded ', Zend_Log::ERR, 'relations.log', true);


            $indexCollection = Mage::getModel('index/process')->getCollection();
            foreach ($indexCollection as $index) {
                /* @var $index Mage_Index_Model_Process */
                $index->reindexAll();
            }

            Mage::log('reindex Proceeded ', Zend_Log::ERR, 'reindex.log', true);

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
        imports products (and also categories, sure) from xlsx
Usage:  php -f importProductsFromExcel.php -- [options]
 
  --file <filepath>       path to csv file
 
  help                   This help
 
USAGE;
    }
}

// Instantiate
$shell = new Itdelight_Shell_ImportProductsFromExcel();

// Initiate script
$shell->run();