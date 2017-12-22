<?php
require_once __DIR__ . '/HostManager.php';
$oHostManager = new HostManager([
    '_vHostName' => 'magento2.docker',
    '_vHostId'   => 'magento2-aligent',
]);
$oHostManager->process();