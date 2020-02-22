<?php


namespace EasySwoole\DocSystem\HttpController;


use EasySwoole\DocSystem\DocLib\Config;
use EasySwoole\DocSystem\DocLib\Exception\PageNotFound;
use EasySwoole\DocSystem\DocLib\Render;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

abstract class DocIndexController extends Controller
{
    public $lang;
    protected abstract function config():Config;
    protected function onRequest(?string $action): ?bool
    {
        $default = $this->config()->getDefaultLanguage();
        $queryPath = $this->request()->getUri()->getPath();
        if($this->request()->getUri()->getPath() == '/'){
            $this->lang = $default;
        }else{
            $allow = $this->config()->getAllowLanguages();
            if (isset($allow[$this->getActionName()])) {
                $this->lang = $this->getActionName();
                /*
                 * 判断是否首页
                 */
                if($queryPath == "/{$this->lang}.html"){
                    $this->index();
                    return false;
                }
            }else{
                $this->lang = $default;
            }
        }
        return true;
    }

    protected function getLanguage(): string
    {
        return $this->lang;
    }

//    protected function markdownFile(): string
//    {
//        $path = $this->request()->getUri()->getPath();
//        return str_replace(["/{$this->getLanguage()}",".html"],['','.md'],$path);
//    }

    function index()
    {
        $render = new Render($this->config());
        $html = $render->home($this->getLanguage(),[]);
        $this->html($html);
    }

    protected function actionNotFound(?string $action)
    {
        $path = $this->request()->getUri()->getPath();
        if (substr($path,-5) =='.html'){
            $render = new Render($this->config());
            $path = str_replace(["/{$this->getLanguage()}",".html"],['','.md'],$path);
            $html = $render->displayFile($path,$this->getLanguage(),[]);
            $this->html($html);
        }else if(substr($path,-1,1) == '/'){
            //如果是访问一个空目录
            $this->display404Page();
        }else{
            $this->response()->withStatus(Status::CODE_NOT_FOUND);
        }
    }

    protected function html(string $content,$status = 200)
    {
        $this->response()->withAddedHeader('Content-type', 'text/html; charset=utf-8');
        $this->response()->withStatus($status);
        $this->response()->write($content);
    }

    protected function display404Page()
    {
        $render = new Render($this->config());
        $html = $render->pageNotFound($this->getLanguage());
        $this->html($html,404);
    }

    protected function onException(\Throwable $throwable): void
    {
        if($throwable instanceof PageNotFound){
            $this->display404Page();
        }else{
            throw $throwable;
        }
    }
}