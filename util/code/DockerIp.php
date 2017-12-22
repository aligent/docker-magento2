<?php


class DockerIp
{
    protected $_dockerName;
    public function __construct()
    {
        $this->_dockerName = $this->getDockerName();
    }
    protected function getDockerName()
    {
        $vBaseName =  basename(dirname(dirname(__DIR__)));
        $vBaseName = str_replace('_','',$vBaseName);
        $vDockerName = $vBaseName . '_web_1';
        return $vDockerName;
    }
    public function getDockerIp()
    {
        $vCommand = "docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' {$this->_dockerName}";
        $vIp =  shell_exec($vCommand);
        $vIp =  trim($vIp);
        if (!$vIp || !ip2long($vIp)){
            return false;
        }
        return $vIp;
    }
}