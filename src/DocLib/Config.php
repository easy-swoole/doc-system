<?php


namespace EasySwoole\DocSystem\DocLib;


use EasySwoole\Spl\SplBean;

class Config extends SplBean
{
    protected $root;
    protected $defaultLanguage;
    protected $tempDir;
    protected $allowLanguages = [];
    /**
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param mixed $root
     */
    public function setRoot($root): void
    {
        $this->root = $root;
    }

    /**
     * @return mixed
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * @param mixed $defaultLanguage
     */
    public function setDefaultLanguage($defaultLanguage): void
    {
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * @return mixed
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    /**
     * @param mixed $tempDir
     */
    public function setTempDir($tempDir): void
    {
        $this->tempDir = $tempDir;
    }

    /**
     * @return array
     */
    public function getAllowLanguages(): array
    {
        return $this->allowLanguages;
    }

    /**
     * @param array $allowLanguages
     */
    public function setAllowLanguages(array $allowLanguages): void
    {
        $this->allowLanguages = $allowLanguages;
    }

    protected function initialize(): void
    {
        if(empty($this->tempDir)){
            $this->tempDir = sys_get_temp_dir();
        }
    }
}