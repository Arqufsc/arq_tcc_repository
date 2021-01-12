<?php

class autoload
{
    public function __construct()
    {
        $rootDir = "./server";
        foreach($this->getFiles($rootDir) as $file)
            require_once $file;

        foreach($this->getDirs() as $dir)
        {
            foreach($this->getFiles($dir) as $file)
                require_once $file;
        }
    }

    private function getDirs()
    {
        return glob("./server/*", GLOB_ONLYDIR);
    }

    private function getFiles($dir)
    {
        return glob("{$dir}/*.php");
    }
}