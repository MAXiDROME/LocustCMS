<?php

$query=@mysqli_query($mysql,"select * from `banners` where `visible`='1' order by `order` asc");
while($row=@mysqli_fetch_assoc($query)) {
    $banner_pic="/img/nophoto.png";
    if (@$row["filename"]!="") {
        $banner_pic=@$bannerspath . @$row["filename"];
    }
    ?>
        <div class="banner">
            <?=@$row["href"]!=''?"<a href='".@$row["href"]."'>":""?>
            <img src="<?=@$banner_pic?>" alt="">
            <div>
                <h3><?=$row["text1"]!=''?$row["text1"]:'&nbsp;'?></h3>
                <h3><?=$row["text2"]!=''?$row["text2"]:'&nbsp;'?></h3>
            </div>
            <?=@$row["href"]!=''?"</a>":""?>
        </div>
    <?
}
