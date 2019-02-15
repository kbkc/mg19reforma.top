<?php
require_once 'abstract.php';
require_once dirname(__FILE__).'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
abstract class Itdelight_Shell_ExcelParser extends Mage_Shell_Abstract
{
    public function parseXML($path_to_XML)
    {
        Mage::getBaseDir('lib');

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($path_to_XML);

        $data = $spreadsheet->getAllSheets();

        return $data;
    }
}