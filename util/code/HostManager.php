<?php

require_once __DIR__ . '/HostManager.php';
require_once __DIR__ . '/DockerIp.php';
require_once __DIR__ . '/Common.php';

class HostManager
{
    protected $_aHostServiceMap;
    protected $_aHostIpMap;
    protected $_aLegacyHosts;
    protected $_vHostId;
    protected $_aInitialConfig = [];
    protected $_vStatePath;
    const SNIPPET_START = '#--- docker-host {host_id}  {host_path}  start --/';
    const SNIPPET_END = '#--- docker-host {host_id}  {host_path}  end   --/';

    public function __construct($aConfig)
    {
        if (!$aConfig) {
            $aConfig = sys_get_temp_dir() . '/docker_mean_bea.json';
        }
        if (!is_array($aConfig)) {
            $aConfig = json_decode(file_get_contents($aConfig));
        }
        if (!is_array($aConfig) || !$aConfig) {
            throw new Exception("no configuration passed");
        }
        $this->_aInitialConfig = $aConfig;
        foreach ($aConfig as $vConfig => $value) {
            $this->$vConfig = $value;
        }
        if (!$this->_vStatePath) {
            $this->_vStatePath = sys_get_temp_dir() . '/docker_mean_bea.json';
        }
    }

    protected function getExistingSnippet()
    {
        return Common::getBetweenString(file_get_contents($this->getHostsPath()),
            $this->getSnippetStart(), $this->getSnippetEnd(), false, false);
    }

    protected function getHostsPath()
    {
        return '/etc/hosts';
        //for testing without modifying root file
//        $vHosts =  dirname(dirname(__DIR__)) . '/hosts';
//        if (!file_exists($vHosts)){
//            copy('/etc/hosts',$vHosts);
//        }
//        return $vHosts;
    }

    protected function canModify()
    {
        return is_writeable($this->getHostsPath());
    }

    protected function getNewSnippetContent()
    {
        $vHostIpMap = '';
        foreach ($this->getHostIpMap() as $vHost => $vIp) {
            $vHostIpMap .=  $vIp.  "  "  . $vHost . "\n" ;
        }
        return $this->getSnippetStart() .
            $vHostIpMap .
            $this->getSnippetEnd();
    }
    protected function getHostIpMap()
    {
        if (is_null($this->_aHostIpMap)){
            foreach ($this->_aHostServiceMap as $vHost => $vService) {
                $oIp = new DockerIp($vService);
                $this->_aHostIpMap[$vHost] = $oIp->getDockerIp();
            }
        }
        return $this->_aHostIpMap;
    }

    protected function getSnippetStart()
    {
        return "\n" . str_replace(['{host_id}','{host_path}'], [$this->_vHostId,dirname(dirname(__DIR__))], self::SNIPPET_START)  . "\n\n";
    }

    protected function getSnippetEnd()
    {
        return "\n" . str_replace(['{host_id}','{host_path}'], [$this->_vHostId,dirname(dirname(__DIR__))], self::SNIPPET_END) . "\n";
    }

    protected function appendSnippet()
    {
        file_put_contents($this->getHostsPath(), $this->getNewSnippetContent(), FILE_APPEND | LOCK_EX);
    }

    protected function replaceSnippet()
    {
        $vContent = $this->getHostsContent();
        $vExistingSnippet = $this->getExistingSnippet();
        $vNewSnippet = $this->getNewSnippetContent();
        $vContent = str_replace($vExistingSnippet, $vNewSnippet, $vContent);
        file_put_contents($this->getHostsPath(), $vContent);
    }

    public function process()
    {
        $vNeedsSudo = sys_get_temp_dir() . '/docker_mean_bee_needs_sudo.flag';
         if (file_exists($vNeedsSudo)) {
            unlink($vNeedsSudo);
        }
        //need to write because of snippet incorrect or no legacy entry found (so new entry)
        if ($this->needToWrite()) {
            echo "need to modify hosts file \n";
            if ($this->canModify()) {
                if ($this->needToReplace()) {
                    $this->replaceSnippet();
                }
                else {
                    $this->appendSnippet();
                }
                echo $this->getHostsPath() .  " entry updated \n";
            }
            else {
                //need to write and need sudo
                echo "need sudo access to write to hosts file " . $this->getHostsPath() . "\n";
                touch($vNeedsSudo);
            }
        }
        //will not write either because snippet is same or legacy entry found so no clear logic to replace them
        else{
            if ($this->getExistingSnippet()) {
                echo $this->getHostsPath() .  " entry already present and is correct \n";
            } else{
                $vPresentHost = $this->legacyHostEntryPresent();
                echo "please remove existing entry of {$vPresentHost} in " . $this->getHostsPath() . "\n";
            }
        }
    }

    protected function needToWrite()
    {
        return $this->needToReplace() || !$this->legacyHostEntryPresent();
    }

    protected function legacyHostEntryPresent()
    {
        $vHostContents = $this->getHostsContent();
        $this->_aLegacyHosts = $this->_aLegacyHosts ?: [];
        foreach ($this->_aLegacyHosts as $vHostSnippet) {
            $iPos = strpos($vHostContents, $vHostSnippet);
            //host entry found
            if ($iPos !== false) {
                return $vHostSnippet;
            }
        }
        return false;
    }

    protected function needToReplace()
    {
        if ($vExistingSnippet = $this->getExistingSnippet()) {
            return $vExistingSnippet != $this->getNewSnippetContent();
        }
        return false;
    }

    protected function getHostsContent()
    {
        return file_get_contents($this->getHostsPath());
    }

    public function test()
    {
        $this->process();
    }

    public function writeToState()
    {
        file_put_contents($this->_vStatePath, json_encode($this->_aInitialConfig));
    }
}