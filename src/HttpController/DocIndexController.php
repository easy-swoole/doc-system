<?php


namespace EasySwoole\DocSystem\HttpController;


use EasySwoole\DocSystem\DocLib\Exception\PageNotFound;
use EasySwoole\DocSystem\DocLib\Render;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

abstract class DocIndexController extends Controller
{
    protected abstract function render():Render;
    protected abstract function getLanguage():string ;

    function index()
    {
        $html = $this->render()->home($this->getLanguage(),[]);
        $this->html($html);
    }


    protected function actionNotFound(?string $action)
    {
        $path = $this->request()->getUri()->getPath();
        if (substr($path,-5) =='.html'){
            $filePath = substr($path,0,-5) . '.md';
            $filePath = ltrim($filePath,'/');
            if ($this->request()->getMethod() == 'POST') {
                $file = ltrim($filePath,'/');
                $page = $this->render()->parserMdFile($file);
                $this->writeJson(Status::CODE_OK, $page, 'success');
            }else{
                $html = $this->render()->displayFile($filePath,$this->getLanguage(),[]);
                $this->html($html);
            }
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

    protected function onException(\Throwable $throwable): void
    {
        if($throwable instanceof PageNotFound){
            $html = $this->render()->pageNotFound($this->getLanguage());
            $this->html($html);
        }else{
            throw $throwable;
        }
    }
}