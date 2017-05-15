<?php
//echo "start";
//print_r($_SESSION["search"]);
//print_r($_GET);

//если не на странице списка или не внутри объекта - обнуляем поля поиска
if(@$_page["id"]!='3' && @$_page["id"]!='8')unset($_SESSION["search"]);

$searchfields=array("pricefrom","priceto","bedrooms","did","cid","sizefrom","sizeto","code","rtype","ctype");

$priceratio=15;//отклонение цены в процентах
$sizeratio=15;//отклонение площади в процентах



//переключаем режим просмотра
if(isset($_GET["view"]) && ($_GET["view"]=='list' || $_GET["view"]=='grid')) {
    setcookie("view",$_GET["view"],time()+86400 * 14,"/");//две недели
    $_COOKIE["view"]=$_GET["view"];
}
if(!isset($_COOKIE["view"]) || ($_COOKIE["view"]!='list' && $_COOKIE["view"]!='grid')) {
    setcookie("view","list",time()+86400 * 14,"/");//две недели
    $_COOKIE["view"]="list";
}
$search_viewmode=$_COOKIE["view"];

//сортировка результатов
if(isset($_GET["sort"]) && ($_GET["sort"]>=0 || $_GET["sort"]<=4)) {
    setcookie("sort",$_GET["sort"],time()+86400 * 14,"/");//две недели
    $_COOKIE["sort"]=$_GET["sort"];
}
if(!isset($_COOKIE["sort"]) || ($_COOKIE["sort"]<0 && $_COOKIE["sort"]>4)) {
    setcookie("sort","0",time()+86400 * 14,"/");//две недели
    $_COOKIE["sort"]="0";
}
$search_sort=$_COOKIE["sort"];


//если есть только cid - значит пришли с карты
if(isset($_GET["cid"]) && $_GET["cid"]!='' && !isset($_GET["searchsubmit"])){
    $_SESSION["search"]["cid"]=$_GET["cid"];
    //unset($_GET["cid"]);
}

//если нажали кнопку "найти жилье" - обнуляем сессионные поля поиска
if(isset($_GET["searchsubmit"]))unset($_SESSION["search"]);


foreach ($searchfields as $field){
    if(isset($_GET[$field]))$_SESSION["search"][$field]=$_GET[$field];
    unset($_GET[$field]);
    if(isset($_SESSION["search"][$field]) && $_SESSION["search"][$field]!='' && $_SESSION["search"][$field]!=0)$_GET[$field]=$_SESSION["search"][$field];
}

//формируем поисковый запрос
$search_sql_query='';
$search_sql_query_nocid='';
foreach ($searchfields as $field) {
    if(isset($_GET[$field]) && $_GET[$field]!='') {
        if($field=='code'){//ид объекта
            $search_sql_query.=" and `objects`.`".$field."` like '%".@mysqli_real_escape_string($mysql,$_GET[$field])."%'";
            $search_sql_query_nocid.=" and `objects`.`".$field."` like '%".@mysqli_real_escape_string($mysql,$_GET[$field])."%'";
        }elseif($field=='cid'){//кондики
            $search_sql_query.=" and `objects`.`" . $field . "`='" . @mysqli_real_escape_string($mysql, $_GET[$field]) . "'";
        } elseif ($field=='pricefrom') {
            $searchprice=@$_GET["pricefrom"]*1;
            if($searchprice>0) {
                $search_sql_query.=" and `objects`.`price`>='" . ($searchprice*(100-$priceratio)/100) . "'";
                $search_sql_query_nocid.=" and `objects`.`price`>='" . ($searchprice*(100-$priceratio)/100) . "'";
            }
        } elseif ($field=='priceto') {
            $searchprice=@$_GET["priceto"]*1;
            if($searchprice>0) {
                $search_sql_query.=" and `objects`.`price`<='" . ($searchprice*(100+$priceratio)/100) . "'";
                $search_sql_query_nocid.=" and `objects`.`price`<='" . ($searchprice*(100+$priceratio)/100) . "'";
            }
        } elseif ($field=='sizefrom') {
            $searchsize=@$_GET["sizefrom"]*1;
            if($searchsize>0) {
                $search_sql_query.=" and `objects`.`size`>='" . ($searchsize*(100-$sizeratio)/100) . "'";
                $search_sql_query_nocid.=" and `objects`.`size`>='" . ($searchsize*(100-$sizeratio)/100) . "'";
            }
        } elseif ($field=='sizeto') {
            $searchsize=@$_GET["sizeto"]*1;
            if($searchsize>0) {
                $search_sql_query.=" and `objects`.`size`<='" . ($searchsize*(100+$sizeratio)/100) . "'";
                $search_sql_query_nocid.=" and `objects`.`size`<='" . ($searchsize*(100+$sizeratio)/100) . "'";
            }
        }elseif($field=='bedrooms') {//спальни
            $bedroomsfrom=$_GET[$field][0]*1;
            $bedroomsto=$_GET[$field][1]*1;

            if($bedroomsfrom!=-1){
                $search_sql_query.=" and `objects`.`".$field."`>=".$bedroomsfrom;
                $search_sql_query_nocid.=" and `objects`.`".$field."`>=".$bedroomsfrom;
            }
            if($bedroomsto!=-1){
                $search_sql_query.=" and `objects`.`".$field."`<=".$bedroomsto;
                $search_sql_query_nocid.=" and `objects`.`".$field."`<=".$bedroomsto;
            }

        } else {
            $search_sql_query.=" and `objects`.`" . $field . "`='" . @mysqli_real_escape_string($mysql, $_GET[$field]) . "'";
            $search_sql_query_nocid.=" and `objects`.`" . $field . "`='" . @mysqli_real_escape_string($mysql, $_GET[$field]) . "'";
        }
    }
}


if(isset($_GET["debug"])) {
    echo "GET<br>";
    print_r($_GET);
    echo "<br>";
    echo "SESSION[search]<br>";
    print_r($_SESSION["search"]);
    echo "<br>";
    echo "Запрос:<br>".$search_sql_query."<br>";
}
