<section>
    <div>
        <ol class="breadcrumb">
            <?
            if ($_page["id"]>2) {
                $query=@mysqli_query($mysql, "SELECT * FROM `pages` WHERE `id`='2'");
                $row=@mysqli_fetch_assoc($query);
                ?>
                <li><a href="/"><?=$row["title"]?></a></li>
                <?
                $pid=$_page["pid"];
                while (1) {
                    $query=@mysqli_query($mysql, "SELECT * FROM `pages` WHERE `id`='" . $pid . "' AND `id`<>'2' AND `visible`='1' AND `published`='1'");
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

            if (0) {//какая-нибудь специальная страница
                ?>
                <li class="active">1234567890</li>
                <?
            } else {
                ?>
                <li class="active"><?=$_page["title"]?></li>
                <?
            }
            ?>
        </ol>
    </div>
</section>
