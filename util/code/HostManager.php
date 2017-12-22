<?php

require_once __DIR__ . '/HostManager.php';
require_once __DIR__ . '/DockerIp.php';
require_once __DIR__ . '/Common.php';

class HostManager
{
    protected $_vHostName;
    protected $_vHostId;
    protected $_aInitialConfig = [];
    protected $_oDockerIp;
    protected $_vStatePath;
    const SNIPPET_START = '#--- docker-host-{host_id}--start';
    const SNIPPET_END = '#--- docker-host-{host_id}--end';

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
        $this->_oDockerIp = new DockerIp();
    }

    protected function getExistingSnippet()
    {
        return Common::getBetweenString(file_get_contents($this->getHostsPath()),
            $this->getSnippetStart(), $this->getSnippetEnd(), false, false);
    }

    protected function getHostsPath()
    {
        return '/etc/hosts';
//        return dirname(dirname(__DIR__)) . '/hosts';
    }

    protected function canModify()
    {
        return is_writeable($this->getHostsPath());
    }

    protected function getNewSnippetContent()
    {
        return $this->getSnippetStart() .
            $this->_oDockerIp->getDockerIp() . "   " . $this->_vHostName .
            $this->getSnippetEnd();
    }

    protected function getSnippetStart()
    {
        return "\n" . str_replace('{host_id}', $this->_vHostId, self::SNIPPET_START) . "\n";
    }

    protected function getSnippetEnd()
    {
        return "\n" . str_replace('{host_id}', $this->_vHostId, self::SNIPPET_END) . "\n";
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
        if ($this->needToWrite()) {
            if ($this->canModify()) {
                if ($this->needToReplace()) {
                    $this->replaceSnippet();
                }
                else {
                    $this->appendSnippet();
                }
            }
            else {
                //need to write and need sudo
                echo "need sudo access to write to hosts file " . $this->getHostsPath() . "\n";
                touch($vNeedsSudo);
            }
        }
        //will not write
        else{
            if (!$this->getExistingSnippet()){
                echo "please remove existing entry of {$this->_vHostName} in " . $this->getHostsPath() . "\n";
            }
        }
    }

    protected function needToWrite()
    {
        return $this->needToReplace() || $this->hostsEntryMissing();
    }

    protected function hostsEntryMissing()
    {
        $iPos = strpos($this->getHostsContent(), $this->_vHostName);
        if ($iPos === false) {
            return true;
        }
        else {
            return false;
        }
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