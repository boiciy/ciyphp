<?php

function ismobile($mob)
{
    return preg_match( '/^1\d{10}$/',$mob);
}
/**
 * 保存文本数据到本地文件。
 */
function savefile($filename, $text) {
    if (!$filename || !$text)
        return false;
    if (makedir(dirname($filename))) {
        $filename = iconv("UTF-8", "GBK", $filename);
        if ($fp = fopen($filename, "w")) {
            if (@fwrite($fp, $text)) {
                fclose($fp);
                return true;
            } else {
                fclose($fp);
                return false;
            } 
        } 
    } 
    return false;
}
