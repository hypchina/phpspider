<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'phpspider\\' => array($baseDir . '/src'),
    'Tests\\' => array($baseDir . '/tests'),
    'QL\\Ext\\Lib\\' => array($vendorDir . '/jaeger/http'),
    'QL\\Ext\\' => array($vendorDir . '/jaeger/querylist-ext-aquery', $vendorDir . '/jaeger/querylist-ext-request'),
    'QL\\' => array($vendorDir . '/jaeger/querylist'),
);