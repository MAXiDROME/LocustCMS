<?
//пользователи
?>
<?

if (isset($_GET["action"]) && $_GET["action"]=="delete_user") {
    @mysqli_query($mysql, "DELETE FROM `users` WHERE `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");

    unset($_GET["action"]);
    unset($_GET["id"]);
}

if (isset($_POST["edit_user_and_exit"])) {//сохраняем
    $_GET["action"]=$_POST["action"];
    $_GET["id"]=$_POST["id"];

    array_walk($_POST, 'makesafesqlstring');

    if (@$_POST["email"]=="") $error["email"].="E-mail не может быть пустым!<br>";
    if (@$_POST["password"]=="") $error["password"].="Не указан пароль!<br>";

    if (!isset($error)) {

        @mysqli_query($mysql, "UPDATE `users` SET
                                                        `email`='" . $_POST["email"] . "',
                                                        `password`='" . $_POST["password"] . "',
                                                        `comment`='" . $_POST["comment"] . "',
                                                        `admin_comment`='" . $_POST["admin_comment"] . "'
                                                WHERE `id`='" . $_GET["id"] . "'");

        unset($_POST);
        unset($_GET);
    }
}//сохранили


if (isset($_GET["action"]) && $_GET["action"]=="edit_user") {//добавляем/изменяем
    if (isset($_GET["id"]) && !isset($_POST["edit_user_and_exit"])) {
        $query=@mysqli_query($mysql, "SELECT * FROM `users` WHERE `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
        $_POST=@mysqli_fetch_assoc($query);
    }

    array_walk($_POST, 'makesafeformstring');

    ?>
    <div class="vertical-align margin-bottom-1">
        <div class="text-left row-phone">
            <h2>Информация о пользователе</h2>
        </div>
    </div>

    <form method="post" action=".">
        <input type="hidden" name="c" value="<?=$c?>"> <input type="hidden" name="action" value="<?=$_GET["action"]?>">
        <input type="hidden" name="id" value="<?=$_GET["id"]?>">

        <div class="row">
            <div class="col-xs-6">
                <div class="form-group<? if (isset($error["email"])) echo " has-error"; ?>">
                    <label class="control-label" for="">E-mail</label>
                    <input type="text" id="" name="email" value="<?=$_POST["email"]?>" class="form-control">
                    <? if (isset($error["email"])) { ?><span class="help-block"><?=$error["email"]?></span><? } ?>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group<? if (isset($error["password"])) echo " has-error"; ?>">
                    <label class="control-label" for="">Пароль</label>
                    <input type="text" id="" name="password" value="<?=$_POST["password"]?>" class="form-control">
                    <? if (isset($error["password"])) { ?><span class="help-block"><?=$error["password"]?></span><? } ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="">Комментарий от пользователя</label>
            <textarea id="comment" name="comment" class="form-control"><?=$_POST["comment"]?></textarea>
        </div>
        <div class="form-group">
            <label class="control-label" for="">Комментарий администратора (скрытый)</label>
            <textarea id="admin_comment" name="admin_comment" class="form-control"><?=$_POST["admin_comment"]?></textarea>
        </div>
        <div class="row margin-top-1">
            <div class="col-sm-6">
                <input type="submit" id="edit_user_and_exit" name="edit_user_and_exit" value="Сохранить и выйти" class="btn btn-phone btn-primary">&nbsp;
                <a href="./?c=<?=$c?>" title="Отмена" class="btn btn-phone btn-default">Отмена</a>
            </div>
        </div>
    </form>
    <?
    //добавили или изменили товар
} else {//показываем список товаров
    //	if(@$cid=="")$cid=0;
    ?>
    <div class="vertical-align m-b-1">
        <div class="text-left row-phone">
            <h2>Пользователи</h2>
        </div>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>E-mail</th>
                <th>Дата регистрации</th>
                <th>Последний вход</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?
            $query=@mysqli_query($mysql, "SELECT `users`.* FROM `users` WHERE 1 ORDER BY `users`.`regdate` DESC");
            while ($row=@mysqli_fetch_assoc($query)) {
                ?>
                <tr>
                    <td><?=@$row["email"]?></td>
                    <td><?=date("d.m.Y", strtotime(@$row["regdate"]))?></td>
                    <td><?=date("d.m.Y", strtotime(@$row["lastlogin"]))?></td>
                    <td nowrap>
                        <div class="pull-right">
                            <div class="btn-group">
                                <a href="./?c=<?=$c?>&amp;action=edit_user&amp;id=<?=@$row["id"]?>" class="btn btn-primary">Редактировать</a>
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="./?c=<?=$c?>&amp;action=block_user&amp;id=<?=@$row["id"]?>">Заблокировать</a>
                                    </li>
                                </ul>
                            </div>
                            <a href="./?c=<?=$c?>&amp;action=delete_user&amp;id=<?=@$row["id"]?>" class="btn btn-danger" onclick="return confirm('Удалить пользователя?')">Удалить</a>
                        </div>
                    </td>
                </tr>
                <?
            }
            ?>
        </tbody>
    </table>
    <?
} //отобразили список

