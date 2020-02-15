<?php


namespace EasySwoole\DocSystem\DocLib;

use EasySwoole\Component\ChannelLock;
use EasySwoole\DocSystem\DocLib\Exception\Exception;
use EasySwoole\DocSystem\DocLib\Exception\PageNotFound;
use EasySwoole\DocSystem\DocLib\Markdown\Parser;

class Render
{
    protected $config;

    function __construct(Config $config)
    {
        $this->config = $config;
    }

    function home(string $language,?array $extraData = null):string
    {
        $data = [
            'lang'=>$language,
            'extra'=>$extraData,
            'allowLanguages'=>$this->config->getAllowLanguages()
        ];
        return $this->smartyRender('index.tpl',$data);
    }

    function displayFile(string $file,string $language,?array $extraData = null)
    {
        $file = ltrim($file,'/');
        $page = $this->parserMdFile($file);
        $sideBar = $this->parserMdFile('sidebar.md');
        $headHtml = $this->config2HtmlTag($page->getConfig());
        $data = [
            'sidebar'=>$sideBar->getHtml(),
            'header'=>$headHtml,
            'content'=>$page->getHtml(),
            'lang'=>$language,
            "headerArray"=>$page->getConfig(),
            'extra'=>$extraData,
            'allowLanguages'=>$this->config->getAllowLanguages()
        ];
        return $this->smartyRender('template.tpl',$data);
    }

    function pageNotFound(string $language)
    {
        return $this->displayFile('404.md',$language);
    }

    protected function parserMdFile(string $file)
    {
        $file = $this->config->getDocRoot()."/$file";
        if(!file_exists($file)){
            throw new PageNotFound("file {$file} not exist");
        }
        return Parser::htmlWithLinkHandel($file);
    }

    protected function smartyRender(string $template,array $data):string
    {
        $ret = ChannelLock::getInstance()->deferLock('smarty',5);
        if($ret){
            $smarty = new \Smarty();
            $smarty->setTemplateDir($this->config->getDocRoot()); //设置模板目录
            $smarty->setCompileDir($this->config->getTempDir() . '/templates_c/');
            $smarty->setCacheDir($this->config->getTempDir() . '/smarty_cache/');
            $smarty->caching = false;
            $smarty->cache_lifetime = 0;
            foreach ($data as $key => $val){
                $smarty->assign($key,$val);
            }
            return $smarty->fetch($template,$cache_id = null, $compile_id = null, $parent = null, $display = false,
                $merge_tpl_vars = true, $no_output_filter = false);
        }else{
            throw new Exception("get smarty lock timeout");
        }
    }

    protected function config2HtmlTag(array $config)
    {
        $html = '';
        //script style
        foreach ($config as $key => $item) {
            if (in_array($key, ['title'])) {
                //只有content的标签
                $html .= "<{$key}>{$item}</{$key}>";
            } else {
                if (in_array($key, ['meta', 'link', 'base'])) {
                    foreach ($item as $value) {
                        $html .= "<{$key}";
                        foreach ($value as $propertyKey => $propertyValue) {
                            //多重标签
                            $html .= " $propertyKey=\"{$propertyValue}\"";
                        }
                        $html .= ">";
                        $html .= "\n";;
                    }
                } else {
                    //style和script标签
                    foreach ($item as $value) {
                        $html .= "<{$key}";
                        foreach ($value as $propertyKey => $propertyValue) {
                            if ($propertyKey == 'content') {
                                continue;
                            }
                            //多重标签
                            $html .= " $propertyKey=\"{$propertyValue}\"";
                        }

                        $html .= ">" . ($value['content'] ?? '') . "</$key>";
                        $html .= "\n";;
                    }
                }
            }
            $html .= "\n";;
        }
        return $html;
    }
}