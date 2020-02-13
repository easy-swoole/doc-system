<?php


namespace EasySwoole\DocSystem\DocLib\Markdown;


use EasySwoole\Spl\SplBean;

class ParserResult extends SplBean
{
    protected $config;
    protected $html;

    /**
     * @return mixed
     */
    public function getConfig():array
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig(?array $config): void
    {
        if(empty($config)){
            $config = [];
        }
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param mixed $html
     */
    public function setHtml($html): void
    {
        $this->html = $html;
    }
}