<?php
class RedInGo_Kid_FakturaController extends Mage_Core_Controller_Front_Action{

    const SHIPMENT_PDF_BASE_DIRECTORY = "/var/www/virtual/bagstar.pl/htdocs/integrator/faktury_pdf/";

    public function IndexAction() {
      $pdfName = $this->getRequest()->getParam('f',false);
      $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

      $sql  = "SELECT * FROM `redingo_kid_faktory`
                    WHERE `plik`= '".$pdfName."'";

      $rows = $connection->fetchAll($sql);
      if( Mage::getSingleton('customer/session')->getCustomer()->getId() == $rows[0]['id_kontr'] )
      {
        $pdfContent = file_get_contents("/var/www/virtual/robert.pl/htdocs/integrator/faktury_pdf/". $pdfName );
        $this->getResponse()->setHeader('Content-type', 'application/pdf');
        $this->getResponse()->setBody($pdfContent);
        return;
      } else
      {
        $this->_redirect('customer/account/login');
      }
    }
}
