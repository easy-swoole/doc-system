# EasySwoole文档系统
EasySwoole文档系统通过 解析md文件编译成html文件,之后交给`smarty`模板引擎进行组装html实现  
文档系统支持seo,多语言,自定义模板,全文搜索功能,可以快速的构建一个功能完善,性能极快的文档系统.  

## 组件安装
```
composer require easyswoole/doc-system
```
## render渲染
render 渲染引擎需要注入一个配置项，然后由这个渲染引擎渲染文档页面
### config配置项
- $root; `文档主目录`
- $defaultLanguage;`默认语言`
- $tempDir;`临时目录`
- $allowLanguages = []; `所有语言列表`

## 初始化模板默认文件 
在`/bootstrap.php`中注入自定义命令:
```php
<?php

\EasySwoole\EasySwoole\Command\CommandContainer::getInstance()->set(new \EasySwoole\DocSystem\DocLib\DocCommand(getcwd()));
```
新增语言以及新增语言默认模板文件:
```
 php easyswoole doc extra Cn 
 php easyswoole doc extra En 
```
主目录将生成Dn,En 目录,并存在默认的模板文件
- 404.md 未找到md文件时输出
- example.md 模板文件例子
- hello.md 文档系统简介
- index.tpl 文档首页模板
- sidebar.md 侧边栏菜单
- template.tpl 文档系统模板文件
::: warning
多语言切换通过更改 127.0.0.1:9501/{语言目录}进行切换
:::
## DocIndexController
`EasySwoole\DocSystem\HttpController\DocIndexController` 控制器继承easyswoole的`EasySwoole\Http\AbstractInterface\Controller`  当用户访问服务器时,通过继承该文件进行处理请求,返回文档数据.   
渲染步骤为:  
- 用户请求服务端(2种情况,一种是存在控制器/文件,直接响应,一种是不存在)
- 存在时正常执行
- 不存在时默认index控制器,index控制器继承`DocIndexController`,并进入`actionNotFound` 方法
- 方法获取请求path,根据配置的文档目录,寻找md文件
- 解析md文件配置(seo配置,标题配置)
- 获取`sideBar.md`(侧边栏菜单)文件数据
- 获取`allowLanguages` 语言配置
- 这些数据全部交给`template.md`,用`smarty`模板引擎渲染
- 输出到页面

###  render方法
render抽象方法需要用户自己实现,在`index`控制器继承`DocIndexController`,然后实现`render`,  
该方法通过配置`EasySwoole\DocSystem\DocLib\Config`返回一个`EasySwoole\DocSystem\DocLib\Render`对象,用于渲染md文件 

例如:  
```php
<?php


namespace App\HttpController;


use EasySwoole\DocSystem\DocLib\Config;
use EasySwoole\DocSystem\DocLib\Render;
use EasySwoole\DocSystem\HttpController\DocIndexController;

class Index extends DocIndexController
{
    protected function render(): Render
    {
        $config = new Config();
        $config->setRoot(EASYSWOOLE_ROOT);
        $config->setAllowLanguages(["Cn"=>"简体中文","En"=>'English']);
        $config->setDefaultLanguage('Cn');
        $config->setTempDir(EASYSWOOLE_TEMP_DIR);
        return new Render($config);
    }
}
```

## 启动模板系统
```
php easyswoole start
```
访问 `localhost:9501` 即可看到首页(index.tpl)  
访问`localhost:9501/hello.html`即可看到hello.md 页面  


## seo配置
每个md文件上面都有一串配置项:  
```
\--- (多了个\号)
title: Hello World //配置title
meta: //配置meta
  - name: description //meta name属性 
    content: EasySwoole Doc System hello page //meta content属性
  - name: keywords
    content: Doc System|Easyswoole
link: 配置link标签(引入css)
  - rel: stylesheet
    type: text/css    
    href: 1.css 
  - rel: stylesheet
    type: text/css    
    href: 2.css
script: 配置script标签(引入js)
  - type: text/javascript
    src: https://track.uc.cn/uaest.js   
  - type: text/javascript
    src: https://track.uc.cn/222.js   
  - content: console.log(1);
\---(多了个\号)
```

## 全文搜索配置
`EasySwoole\DocSystem\DocLib\DocMdSearchParser::parserDoc2JsonUrlMap(语言目录)` 可通过解析不同语言模板目录的sidebar.md进行获取其所有的连接md文件,并进行组装json,用于前端的全文搜索功能  
可在 `initialize` 进行缓存json数据 

## 自定义模板系统
通过重写`EasySwoole\DocSystem\HttpController\DocIndexController` 的方法,可自己实现其他自定义逻辑,例如:

### 自定义语言切换方式
本组件默认为第一个如今的第一个目录名作为当前语言,你也可以通过重写`onRequest`方法,讲语言设置改为cookie形式获取