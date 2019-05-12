<?php
require 'init.php';
$mydata = new ciy_data();
ciy_runJSON();
function json_uperr() {
    $post = new ciy_post();
    savelog($post->get('tit'),'UID:'.cookie('auid').'.ERR:'.$post->get('err').'.Msg:'.$post->get('msg').'.Stack:'.$post->get('stack'));
    return succjson();
}