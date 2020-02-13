<?php


namespace App\DocLib;


use App\DocLib\Markdown\Parser;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Utility\File;
use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDom;

class DocMdSearchParser
{
    static function scan()
    {
        $list = Config::getInstance()->getConf('DOC.LANGUAGE');
        foreach ($list as $lan){
            self::parserFiles2JsonUrlMap($lan);
        }
    }

    protected static function parserFiles2JsonUrlMap($lan){
        $jsonList = [];
        $sidebarHtml = self::getSidebar($lan);
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
            $jsonList[] = [
                'id'  => $id,
                'title'  => $name,
                'content'  => self::getMdContent($path,$lan),
                'link'  => Parser::mdLink2Html($path),
            ];
            $id++;
        }
        $jsonPath = EASYSWOOLE_ROOT."/Static/keyword{$lan}.json";

        File::createFile($jsonPath,json_encode($jsonList,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
    }

    protected static function getSidebar($lan)
    {
        $docPath = Config::getInstance()->getConf('DOC.PATH');

        $sidebarPath = EASYSWOOLE_ROOT."/{$lan}/sidebar.md";
        //获取sideBar的parserHtml
        $sideBarResult = Parser::html($sidebarPath);
        $html = $sideBarResult->getHtml();
        return $html;
    }

    protected static function getMdContent($path,$lan){
        //这边的path已经存在了/斜杆
        $filePath = EASYSWOOLE_ROOT."/$lan{$path}";

        if (!file_exists($filePath)) {
            return null;
        }
        $result = Parser::htmlWithLinkHandel($filePath);
        $html = $result->getHtml();
        return strip_tags($html);

    }
}