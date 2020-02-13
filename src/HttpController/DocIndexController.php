<?php


namespace EasySwoole\DocSystem\HttpController;


use EasySwoole\DocSystem\DocLib\Exception\PageNotFound;
use EasySwoole\DocSystem\DocLib\Render;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\DocSystem\DocLib\Config as DocConfig;
use EasySwoole\Http\Message\Status;

class DocIndexController extends Controller
{

    public $language;

    function index()
    {
        $html = $this->render()->home();
        $this->html($html);
    }

    /*
    * 语言自动识别
    */
    protected function onRequest(?string $action): ?bool
    {
        $lan = $this->request()->getCookieParams('language');
        if(empty($lan)){
            $lan = $this->request()->getRequestParam('language');
        }
        if(empty($lan)){
            //从用户浏览器ua的accept encode 识别
        }
        $allow = Config::getInstance()->getConf('DOC.LANGUAGE');
        if(in_array($lan,$allow,true)){
            $this->language = $lan;
        }else{
            $this->language = Config::getInstance()->getConf("DOC.DEFAULT_LANGUAGE");
        }
        return true;
    }


    protected function actionNotFound(?string $action)
    {
        $path = $this->request()->getUri()->getPath();
        if (substr($path,-5) =='.html'){
            $filePath = substr($path,0,-5) . '.md';
            $filePath = ltrim($filePath,'/');
            $html = $this->render()->displayFile($filePath);
            $this->html($html);
        }else{
            $this->response()->withStatus(Status::CODE_NOT_FOUND);
        }
    }

    protected function html(string $content)
    {
        $this->response()->withAddedHeader('Content-type', 'text/html; charset=utf-8');
        $this->response()->withStatus(Status::CODE_OK);
        $this->response()->write($content);
    }

    protected function render():Render
    {
        $config = new DocConfig();
        $config->setLanguage($this->language);
        $render = new Render($config);
        return $render;
    }

    protected function onException(\Throwable $throwable): void
    {
        if($throwable instanceof PageNotFound){
            $html = $this->render()->pageNotFound();
            $this->html($html);
        }else{
            $this->response()->write($throwable->getMessage());
            Trigger::getInstance()->throwable($throwable);
        }
    }
}