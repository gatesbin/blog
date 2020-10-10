<?php


namespace TechOnline\Utils;


class ShellUtil
{
    public static function runRealtime($cmd)
    {
        echo "\n[ShellRun] $cmd\n";
        if (($fp = popen($cmd, "r"))) {
            while (!feof($fp)) {
                echo fread($fp, 1024);
                flush();
            }
            fclose($fp);
        }
    }
}