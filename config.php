<?php
@session_start();

date_default_timezone_set('Asia/Yekaterinburg');

define("LocustCMS","1.0");

define("sendpulse_id", "");
define("sendpulse_secret", "");
define("sendpulse_addressbook_id1", "");//подписчики
define("sendpulse_addressbook_id2", "");//зарегистрированные пользователи

$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__);

$mysql=@mysqli_connect('localhost','','','');
mysqli_query($mysql,"set names utf8");

$query=mysqli_query($mysql,"select * from `config` where `id`='1'");
$_config=mysqli_fetch_assoc($query);

$imgpath="/public/";

$newspath=$imgpath."news/";
$n_thumb_w=400;
$n_thumb_h=300;

$bannerspath=$imgpath."banners/";
$b_thumb_w=800;
$b_thumb_h=200;

