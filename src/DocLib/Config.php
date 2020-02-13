<?php


namespace EasySwoole\DocSystem\DocLib;


use EasySwoole\Spl\SplBean;

class Config extends SplBean
{
    protected $docRoot;
    protected $language;
    protected $tempDir;
    protected $allowLanguages = [];
    /**
     * @return mixed
     */
    public function getDocRoot()
    {
        return $this->docRoot;
    }

    /**
     * @param mixed $docRoot
     */
    public function setDocRoot($docRoot): void
    {
        $this->docRoot = $docRoot;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language): void
    {
        $this->language = $language;
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