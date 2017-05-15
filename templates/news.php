<?
include_once 'includes/header.inc.php';

include_once 'includes/page.header.inc.php';
?>
    <main>
        <?
        include_once 'includes/breadcrumbs.inc.php';
        ?>
        <section>
<?
if(isset($_item)){//показываем новость

    ?>
    <h1><?=$_item["title"]?></h1>
    <small><?=date("d.m.Y",strtotime(@$_item["adddate"]))?></small>
    <?
    if(@$_item["filename"]!=''){
        ?>
        <img src="<?=@$newspath.@$_item["filename"]?>" alt="<?=@$_item["title"]?>">
        <?
    }
    ?>
    <?=$_item["content"]?>
    <?

}else{//показываем список новостей

    ?>
    <h1><?=$_page["title"]?></h1>
    <?
    $query=@mysqli_query($mysql,"select * from `news` where `pid`='".@$_page["id"]."' and `visible`='1' and `published`='1' order by `adddate` desc");
    $total_pages=ceil(@mysqli_num_rows($query)/@$_config["pagesize"]);
    $query=@mysqli_query($mysql,"select * from `news` where `pid`='".@$_page["id"]."' and `visible`='1' and `published`='1' order by `adddate` desc limit ".(($page-1)*@$_config["pagesize"]).",".@$_config["pagesize"]."");
    while($row=@mysqli_fetch_assoc($query)){
        $newsimg="/img/nophoto.png";
        if(@$row["filename"]!='')$newsimg=@$newspath.@$row["filename"];
        ?>
        <div class="news">
            <a href="<?="/".@$_page["rewrite"].@$row["rewrite"]?>">
                <img src="<?=$newsimg?>" alt="<?=@$row["title"]?>">
                <?=@$row["title"]?>
                <small><?=date("d.m.Y",strtotime(@$row["adddate"]))?></small>
                <p><?=@$row["info"]?></p>
            </a>
        </div>
        <?

    }

    if($total_pages>1) draw_navigation($total_pages,$page,"?page={p}");

    ?>
    <p><?=$_page["content"]?></p>
    <?

    }?>
        </section>
    </main>
<?
include_once 'includes/footer.inc.php';
