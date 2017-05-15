<section>
    <div>
        <ol class="breadcrumb">
<?
if($_page["id"]>1) {

    $query=@mysqli_query($mysql, "select * from `pages` where `id`='2'");
    $row=@mysqli_fetch_assoc($query);
    ?>
    <li><a href="/"><?=$row["title"]?></a></li>
    <?
    $pid=$_page["pid"];
    while (1) {
        $query=@mysqli_query($mysql, "select * from `pages` where `id`='" . $pid . "' and `id`<>'2' and `visible`='1' and `published`='1'");
        if ($row=@mysqli_fetch_assoc($query)) {
            $breadcrumbs[]=$row;
            $pid=$row["pid"];
        } else {
            break;
        }
    }
    for ($i=@count(@$breadcrumbs)-1; $i>=0; $i--) {
        ?>
        <li><a href="/<?=$breadcrumbs[$i]["rewrite"]?>"><?=$breadcrumbs[$i]["title"]?></a></li>
        <?
    }
}

if(0) {//какая-нибудь специальная страница
    ?>
    <li class="active">1234567890</li>
    <?
}else{
    ?>
    <li class="active"><?=$_page["title"]?></li>
    <?
}
?>
        </ol>
    </div>
</section>
