<?php


class DockerIp
{
    protected $_dockerName;
    protected $_vServiceName;
    public function __construct($vServiceName)
    {
        $this->_vServiceName = $vServiceName;
        $this->_dockerName = $this->getDockerName();
    }
    protected function getDockerName()
    {
        $vBaseName =  basename(dirname(dirname(__DIR__)));
        //TODO: investigate why _ is part of name in same cases mf_docker but not in other
//        $vBaseName = str_replace('_','',$vBaseName);
        $vDockerName = $vBaseName . "_{$this->_vServiceName}_1";
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