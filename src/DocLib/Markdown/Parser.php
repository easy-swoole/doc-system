<?php


namespace EasySwoole\DocSystem\DocLib\Markdown;


use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDom;

class Parser
{
    public static function html(string $path): ParserResult
    {
        $result = new ParserResult();
        $content = '';
        $head = '';
        $file = fopen($path, "r");
        $isInHead = false;
        while (is_resource($file) && !feof($file)) {
            $line = fgets($file);
            if ($isInHead) {
                if (strlen(trim($line))==3 && substr($line, 0, 3) == '---') {
                    $isInHead = false;
                } else {
                    $head = $head . $line;
                }
            } else {
                if (strlen(trim($line))==3 && substr($line, 0, 3) == '---') {
                    $isInHead = true;
                } else {
                    $content = $content . $line;
                }
            }
        }
        fclose($file);
        $result->setConfig(yaml_parse($head));
        $parsedown = new \Parsedown();
        $html = $parsedown->text($content);;
        $result->setHtml($html);
        return $result;
    }

    public static function htmlWithLinkHandel($path)
    {
        $result = self::html($path);
        $result->setHtml(self::htmlLinkHandel($result->getHtml()));
        return $result;
    }

    /**
     * 额外处理html内容
     * handelHtml
     * @param $html
     * @return mixed
     * @author tioncico
     * Time: 下午2:55
     */
    protected static function htmlLinkHandel($html)
    {
        $dom = HtmlDomParser::str_get_html($html);
        //处理链接类标签
        $aList = $dom->find('a');
        /**
         * @var $a SimpleHtmlDom
         */
        foreach ($aList as $a) {
            $info = pathinfo($a->href);
            if (isset($info['extension']) && ($info['extension'] == 'md')) {
                $a->href = self:: mdLink2Html($a->href);
            }
        }

        //处理h类标签
        $hList = $dom->find('h1,h2,h3,h4,h5,h6');
        foreach ($hList as $h) {
            $h->setAttribute('id',$h->getNode()->textContent);
        }

        return $dom->html();
    }

    static function mdLink2Html($link)
    {
        if (substr($link, -3) == '.md') {
            return substr($link, 0, -3) . '.html';
        }
        return $link;
    }
}