<?php


namespace EasySwoole\DocSystem\DocLib;


use EasySwoole\Spl\SplBean;

class Config extends SplBean
{
    protected $docRoot;
    protected $language;

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
}