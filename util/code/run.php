<?php
require_once __DIR__ . '/HostManager.php';
$vConfigPath = dirname(dirname(__DIR__)) .'/magento/docker/host_manager_config.php';
if (file_exists($vConfigPath)){
    $aConfig = include $vConfigPath;
}
else{
    $aConfig = [
        '_aHostServiceMap' => [
            'magento2.docker site-2-magento2.docker' => 'web',
            'db-magento2.docker' => 'db',
        ],
        '_aLegacyHosts' => [
            'magento2.docker',
            'db-magento2.docker',
            'site-2-magento2.docker',
        ],
        '_vHostId'   => 'magento2-aligent',
    ];
}
$oHostManager = new HostManager($aConfig);
$oHostManager->process();