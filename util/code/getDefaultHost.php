<?php
function getWebHostEntry()
{
    $bDoNotRun = true;
    $aConfig = require_once __DIR__ . '/run.php';
    foreach ($aConfig['_aHostServiceMap'] as $vHostEntry => $vServiceType) {
        if ($vServiceType == 'web') {
            return $vHostEntry;
        }
    }
}

$vDomain = current(explode(' ', getWebHostEntry()));
echo $vDomain;