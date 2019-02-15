<?php
/**
 * Created by PhpStorm.
 * User: gabriel.croitoru
 * Date: 12.02.2016
 * Time: 16:53
 */

$installer = $this;

$installer->startSetup();

$installer->getConnection()->changeColumn(
        $this->getTable('zitec_dpd_tablerate'),
        'method',
        'method',
        "varchar(8) NOT NULL DEFAULT 0"
);
$installer->endSetup();