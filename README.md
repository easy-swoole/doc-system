# EasySwoole文档系统
## 创建语言目录
```
php easyswoole doc create LANGUAGE 
```
即可成功创建目录。例如创建Cn语言目录：
```
php easyswoole doc create Cn 
```
我们可以在项目根目录看到目录结构如下
```
Cn
├── 404.md        -- 默认404页面
├── example.md    -- 文档例子页面
├── hello.md      -- 文档例子页面
├── index.md      -- 默认语言首页
├── sideBar.md    -- 默认导航栏
└── template.md   -- 默认文档模板
```
添加语言配置到dev.php
```
"DOC" => [
    "LANGUAGE" => ["Cn"],
    "DEFAULT_LANGUAGE"=>"Cn"
]
```
启动Easyswoole即可看到文档。

## 首页模板修改
编辑对应语言下的index.md即可
## 创建章节
## 页面seo
## 全局配置项