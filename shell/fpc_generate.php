<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/extension_fpc
 * @version   1.0.87
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



require_once 'abstract.php';

class Mirasvit_Shell_Fpc extends Mage_Shell_Abstract
{
    public function run()
    {
        $storeId = false;
        if ($this->getArg('store_id')) {
            $storeId = $this->getArg('store_id');
        }
        if ($this->getArg('generate')) {
            Mage::getSingleton('fpccrawler/system_addurlsincrawler_worker')->run(true);
        } elseif ($this->getArg('generate_and_crawl_both')) {
            Mage::getSingleton('fpccrawler/system_addurlsincrawler_worker')->run(true);
            Mage::getModel('fpccrawler/crawler_crawl')->doRun(true, true, $storeId);
            Mage::getModel('fpccrawler/crawlerlogged_crawl')->doRun(true, true, $storeId);
        } elseif ($this->getArg('crawl_both')) {
            Mage::getModel('fpccrawler/crawler_crawl')->doRun(true, true, $storeId);
            Mage::getModel('fpccrawler/crawlerlogged_crawl')->doRun(true, true, $storeId);
        } elseif ($this->getArg('crawl_not_logged_in')) {
            Mage::getModel('fpccrawler/crawler_crawl')->doRun(true, true, $storeId);
        } elseif ($this->getArg('crawl_logged_in')) {
            Mage::getModel('fpccrawler/crawlerlogged_crawl')->doRun(true, true, $storeId);
        } else {
            echo $this->usageHelp();
        }
    }

    public function _validate()
    {
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f fpc_generate.php --[options]  (example: php fpc_generate.php --crawl_not_logged_in --store_id 2)  

  generate                  Generate crawler urls without adding in cache (use data from admin panel)
  generate_and_crawl_both   Generate and add in cache (for generation use data from admin panel)
  crawl_both                Crawl all generated urls from crawler table (it can get a lof of time)
  crawl_logged_in           Crawl logged in generated urls from crawler table (it can get a lof of time)
  crawl_not_logged_in       Crawl not logged in generated urls from crawler table (it can get a lof of time)
  store_id                  Use only as additional argument for crawl_both, crawl_logged_in, crawl_not_logged_in
                                (example: store_id 2)
  help                      This help

USAGE;
    }
}

$shell = new Mirasvit_Shell_Fpc();

$shell->run();
