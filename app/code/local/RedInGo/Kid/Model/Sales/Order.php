<?php
class RedInGo_Kid_Model_Sales_Order extends Mage_Sales_Model_Order
{

    public function isStateProtected($state)    {
        if (empty($state)) {
            return false;
        }
        return self::STATE_CLOSED == $state;
    }

    public function getInvoicePDF()
    {
      $resource = Mage::getSingleton('core/resource');
    	$readConnection = $resource->getConnection('core_read');
      'vu2027.admin.s41.mhost.eu/redingo_integrator/faktura?f=';
      $plik = $readConnection->fetchAll(
       "SELECT `plik`, `nr`
        FROM `redingo_kid_faktory`
        WHERE `nr_zam` = '".$this->getRealOrderId()."'
        LIMIT 1"
      );
      return $plik[ 0 ];
    }
    public function getInvoicePDFUrl()
    {
      $tmp = $this->getInvoicePDF();
      printf('<a href="%sredingo_integrator/faktura?f=%s" > %s </a>', Mage::getBaseUrl(), $tmp['plik'], $tmp['nr']  );
    }
}
