<?php

?>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Дата</th>
            <th>Пользователь</th>
            <th>Действие</th>
        </tr>
    </thead>
    <tbody>
<?php
$query=@mysqli_query($mysql,"select `admin_log`.*,`admin_users`.`login` as `user_login` from `admin_log` left join `admin_users` on `admin_users`.`id`=`admin_log`.`user` order by `admin_log`.`timestamp` desc");
$total_pages=ceil(@mysqli_num_rows($query)/$_config["pagesize"]);
if($page=="")$page=1;
$query=@mysqli_query($mysql,"select `admin_log`.*,`admin_users`.`login` as `user_login` from `admin_log` left join `admin_users` on `admin_users`.`id`=`admin_log`.`user` order by `admin_log`.`timestamp` desc limit ".(($page-1)*$_config["pagesize"]).",".$_config["pagesize"]."");
while($row=@mysqli_fetch_assoc($query)){
    ?>
    <tr>
        <td><?=date("d.m.Y H:i:s",strtotime($row["timestamp"]))?></td>
        <td><?=$row["user_login"]?></td>
        <td><?=$row["action"]?></td>
    </tr>
    <?
}

?>
</tbody>
</table>
<?php
if($total_pages>1)draw_navigation($total_pages,$page,"./?c=".$c."&amp;page={p}");