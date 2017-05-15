<?php

if(!defined("LocustCMS"))die("err403");

$mRequestUri=$_SERVER["REQUEST_URI"]; //получаем REQUEST_URI
if ($mRequestUri=='/') {//если пользователь обратился к главной странице
    $mPageUrl=$mRequestUri;
} else {
    if ($_SERVER['QUERY_STRING']) {
        $mPageUrl=preg_replace(array('/^\//', '/\/?\?' . RegexpEscape($_SERVER['QUERY_STRING']) . '$/'), array('', ''), $mRequestUri) . '/';
    } else {
        $mPageUrl=preg_replace(array('/^\//', '/\/?\??$/'), array('', ''), $mRequestUri) . '/';
    }
}

$query=@mysqli_query($mysql, "select *, substring('$mPageUrl' from length(`rewrite`)+1) `trail` from `pages` where `published`='1' and '$mPageUrl' like concat(`rewrite`,'%') order by length(substring('$mPageUrl' from length(`rewrite`)+1))");
$_page=@mysqli_fetch_assoc($query);

if (@$_page["id"]=="") {//если страница не найдена - показываем 404
    pagenotfound();
}
if ($_page['trail']) {
    $urlParams=explode('/', $_page['trail']);
    if ($urlParams[count($urlParams)-1]=="") {
        unset($urlParams[count($urlParams)-1]);
    }
}

//выцепляем страницы
$page=@mysqli_real_escape_string($mysql, @$_GET["page"]);
if ($page==0) $page=1;
//выцепили страницы

$meta_title=$_page["meta_title"]!='' ? $_page["meta_title"] : $_page["title"];
$meta_description=$_page["meta_description"]!='' ? $_page["meta_description"] : $_config["meta_description"];
$meta_keywords=$_page["meta_keywords"]!='' ? $_page["meta_keywords"] : $_config["meta_keywords"];

if ($_page["template"]=='news') {//если новости
    if(@$urlParams[0]!='') {
        $query=@mysqli_query($mysql, "select * from `news` where `pid`='" . $_page["id"] . "' and `published`='1' and `rewrite`='" . $urlParams[0] . "'");
        if ($_item=@mysqli_fetch_assoc($query)) {
            $meta_title=$_item["title"];
            $meta_title=$_item["meta_title"]!='' ? $_item["meta_title"] : $meta_title;
            $meta_description=$_item["meta_description"]!='' ? $_item["meta_description"] : $meta_description;
            $meta_keywords=$_item["meta_keywords"]!='' ? $_item["meta_keywords"] : $meta_keywords;
        } else {
            pagenotfound();
        }
    }
}

//$pagesarr - массив id текущей страницы и ее родителей
parent_tree($_page["id"]);

function pagenotfound(){
    global $mysql,$_page,$_config,$mPageUrl,$meta_description,$meta_keywords,$meta_title;
    header("HTTP/1.0 404 Not Found");
    $query=@mysqli_query($mysql, "select *, substring('$mPageUrl' from length(`rewrite`)+1) `trail` from `pages` where `id`='1'");
    $_page=@mysqli_fetch_assoc($query);

    $meta_title=$_page["meta_title"]!='' ? $_page["meta_title"] : $_page["title"];
    $meta_description=$_page["meta_description"]!='' ? $_page["meta_description"] : $_config["meta_description"];
    $meta_keywords=$_page["meta_keywords"]!='' ? $_page["meta_keywords"] : $_config["meta_keywords"];
}
