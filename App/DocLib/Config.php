<?php


namespace App\DocLib;


use EasySwoole\Spl\SplBean;

class Config extends SplBean
{
    protected $docRoot;
    protected $homePage = 'index.md';
    protected $template = 'template';
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
     * @return string
     */
    public function getHomePage(): string
    {
        return $this->homePage;
    }

    /**
     * @param string $homePage
     */
    public function setHomePage(string $homePage): void
    {
        $this->homePage = $homePage;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
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