<?php
$installer = $this;
$installer->startSetup();

$sql=<<<SQLTEXT
        
CREATE TABLE IF NOT EXISTS `{$installer->getTable('redingo_status')}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kid_status` varchar(1) NOT NULL,
  `status` varchar(255) NOT NULL,
  `opis` text NOT NULL,
  `wyslij` tinyint(1) NOT NULL,
  `dostawa` tinyint(1) NOT NULL,
  `faktura` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin2 AUTO_INCREMENT=6 ;

--
-- Zrzut danych tabeli `redingo_status`
--

INSERT INTO `{$installer->getTable('redingo_status')}` (`id`, `kid_status`, `status`, `opis`, `wyslij`, `dostawa`, `faktura`) VALUES
(1, 'P', 'paypal_reversed', '', 1, 1, 1),
(5, 'C', 'canceled', '', 0, 0, 0),
(4, 'R', 'processing', '', 1, 1, 0)
SQLTEXT;

$installer->run($sql);

$installer->endSetup();










