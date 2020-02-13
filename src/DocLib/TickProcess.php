<?php


namespace EasySwoole\DocSystem\DocLib;

use EasySwoole\Component\Process\AbstractProcess;

class TickProcess extends AbstractProcess
{
    protected function run($arg)
    {
        go(function (){
            while (1){
                DocMdSearchParser::scan();
                \co::sleep(10);
            }
        });
    }
}