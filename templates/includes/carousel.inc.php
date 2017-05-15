<?php

$query=@mysqli_query($mysql,"select * from `carousel` where `visible`='1' order by `order`");
while($row=@mysqli_fetch_assoc($query)){
    ?>
    <div class="item">
        <h1><?=_pg($row,"title")?></h1>
        <p class="offset-top-25"><?=$row["content"]?></p>
    </div>
    <?
}
