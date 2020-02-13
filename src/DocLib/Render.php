<?php


namespace EasySwoole\DocSystem\DocLib;

use EasySwoole\DocSystem\DocLib\Exception\PageNotFound;
use EasySwoole\DocSystem\DocLib\Markdown\Parser;

class Render
{
    protected $config;

    function __construct(Config $config)
    {
        $this->config = $config;
    }

    function home():string
    {
        return $this->parserMdFile('index.md')->getHtml();
    }

    function displayFile(string $file)
    {
        $page = $this->parserMdFile($file);
        $sideBar = $this->parserMdFile('sideBar.md');
        $templatePage = $this->parserMdFile('template.md');
        $config = $this->configMerge($page->getConfig(),$templatePage->getConfig());
        $headHtml = $this->config2HtmlTag($config);
        return str_replace(['{$header}', '{$sidebar}', '{$content}', '{$lang}'], [$headHtml , $sideBar->getHtml(), $page->getHtml(), $this->config->getLanguage()],$templatePage->getHtml());
    }

    function pageNotFound()
    {
        return $this->displayFile('404.md');
    }


    protected function parserMdFile(string $file)
    {
        $file = EASYSWOOLE_ROOT."/{$this->config->getLanguage()}/$file";
        if(!file_exists($file)){
            throw new PageNotFound("file {$file} not exist");
        }
        return Parser::htmlWithLinkHandel($file);
    }

    protected function configMerge(array $config, array $globalConfig)
    {
        return [
            'title'=>$config['title']??$globalConfig['title']??'',
            'meta'=>$config['meta']??$globalConfig['meta']??[],
            'base'=>array_merge($config['base']??[],$globalConfig['base']??[]),
            'link'=>array_merge($config['link']??[],$globalConfig['link']??[]),
            'script'=>array_merge($config['script']??[],$globalConfig['script']??[]),
        ];
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
                        $html .= "/>";
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