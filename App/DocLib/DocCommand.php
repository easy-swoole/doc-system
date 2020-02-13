<?php


namespace App\DocLib;


use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\EasySwoole\Command\Utility;
use EasySwoole\Utility\File;

class DocCommand implements CommandInterface
{
    public function commandName(): string
    {
        return 'doc';
    }

    public function exec(array $args): ?string
    {
        if(isset($args[0]) && $args[0] == 'extra'){
            if(isset($args[1])){
                $lang = $args[1];
                $files = File::scanDirectory(EASYSWOOLE_ROOT.'/App/DocLib/Resource');
                foreach ($files['files'] as $file){
                    $info = pathinfo($file);
                    Utility::releaseResource($file, EASYSWOOLE_ROOT ."/{$lang}/". $info['basename']);
                }
                return "{$lang} 语言目录创建成功";
            }
        }
        return $this->help($args);
    }

    public function help(array $args): ?string
    {
        return "php easyswoole doc create LANGUAGE 创建语言目录";
    }

}