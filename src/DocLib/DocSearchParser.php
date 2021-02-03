<?php


namespace EasySwoole\DocSystem\DocLib;


use EasySwoole\DocSystem\DocLib\Markdown\Parser;
use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDom;

class DocSearchParser
{
    static function parserDoc2JsonUrlMap(string $docRoot,string $lang):array
    {
        $jsonList = [];
        $sidebarHtml = self::getSidebar($docRoot."/".$lang);
        $dom = HtmlDomParser::str_get_html($sidebarHtml);
        //获取导航栏所有md链接
        $aList = $dom->find('a');
        $id = 1;
        /**
         * @var $a SimpleHtmlDom
         */
        foreach ($aList as $a) {
            $path = $a->href;
            $name = $a->getNode()->textContent;
            $tempList = [
                'id'  => $id,
                'title'  => strtolower($name),
                'content'  => self::getMdContent($docRoot . '/' . $lang . '/' .$path),
                'link'  => '/' . Parser::mdLink2Html($path),
            ];
            if ($tempList['content']) {
                $jsonList[] = $tempList;
            }
            $id++;
        }
        return $jsonList;
    }

    protected static function getSidebar($docRoot):string
    {
        $sidebarPath = $docRoot."/sidebar.md";
        //获取sideBar的parserHtml
        $sideBarResult = Parser::html($sidebarPath);
        $html = $sideBarResult->getHtml();
        return $html;
    }

    protected static function getMdContent(string $file)
    {
        if (!file_exists($file)) {
            return null;
        }
        $result = Parser::htmlWithLinkHandel($file);
        $html = $result->getHtml();
        return strip_tags($html);
    }
}
