<?
//админы

    if (isset($_GET["action"]) && $_GET["action"]=="delete_user") {
        if ($_GET["id"]==1) $_GET["id"]=0;
        @mysqli_query($mysql, "DELETE FROM `admin_users` WHERE `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");

        unset($_GET["action"]);
        unset($_GET["id"]);
    }

    if (isset($_POST["edit_user_and_exit"])) {//сохраняем
        $_GET["action"]=$_POST["action"];
        $_GET["id"]=$_POST["id"];

        array_walk($_POST, 'makesafesqlstring');

        if (@$_POST["login"]=="") $error["login"].="Логин не может быть пустым!<br>";
        if (@$_POST["password"]=="") $error["password"].="Не указан пароль!<br>";

        if (!isset($error)) {
            if ($_GET["id"]=='') {
                @mysqli_query($mysql, "INSERT INTO `admin_users` SET
                                                        `login`='" . $_POST["login"] . "',
                                                        `password`='" . $_POST["password"] . "',
                                                        `name`='" . $_POST["name"] . "',
                                                        `comment`='" . $_POST["comment"] . "',
                                                        `role`='" . json_encode($_POST["role"]) . "'
                                                ");

            } else {

                @mysqli_query($mysql, "UPDATE `admin_users` SET
                                                        `login`='" . $_POST["login"] . "',
                                                        `password`='" . $_POST["password"] . "',
                                                        `name`='" . $_POST["name"] . "',
                                                        `comment`='" . $_POST["comment"] . "',
                                                        `role`='" . json_encode($_POST["role"]) . "'
                                                WHERE `id`='" . $_GET["id"] . "'");
            }
            unset($_POST);
            unset($_GET);
        }
    }//сохранили


    if (isset($_GET["action"]) && $_GET["action"]=="edit_user") {//добавляем/изменяем
        if ($_GET["id"]==1) $_GET["id"]=0;
        if (isset($_GET["id"]) && !isset($_POST["edit_user_and_exit"])) {
            $query=@mysqli_query($mysql, "SELECT * FROM `admin_users` WHERE `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
            $_POST=@mysqli_fetch_assoc($query);
            $_POST["role"]=@json_decode($_POST["role"]);
        }

        array_walk($_POST, 'makesafeformstring');

        ?>
        <div class="vertical-align margin-bottom-1">
            <div class="text-left row-phone">
                <h2>Информация о пользователе</h2>
            </div>
        </div>

        <form method="post" action=".">
            <input type="hidden" name="c" value="<?=$c?>">
            <input type="hidden" name="action" value="<?=$_GET["action"]?>">
            <input type="hidden" name="id" value="<?=$_GET["id"]?>">

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group<? if (isset($error["login"])) echo " has-error"; ?>">
                        <label class="control-label" for="">Логин</label>
                        <input type="text" id="" name="login" value="<?=$_POST["login"]?>" class="form-control">
                        <? if (isset($error["login"])) { ?><span class="help-block"><?=$error["login"]?></span><? } ?>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group<? if (isset($error["password"])) echo " has-error"; ?>">
                        <label class="control-label" for="">Пароль</label>
                        <input type="text" id="" name="password" value="<?=$_POST["password"]?>" class="form-control">
                        <? if (isset($error["password"])) { ?>
                            <span class="help-block"><?=$error["password"]?></span><? } ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="">Имя</label>
                <input type="text" id="" name="name" class="form-control" value="<?=$_POST["name"]?>">
            </div>
            <div class="form-group">
                <label class="control-label" for="">Комментарий</label>
                <textarea id="comment" name="comment" class="form-control"><?=$_POST["comment"]?></textarea>
            </div>
            <div class="form-group">
                <label class="control-label" for="">Доступ</label>
                <div class="row">
                    <?
                    foreach ($_cms_pages as $_name=>$_cat) {
                        ?>
                        <div class="col-xs-12 col-sm-4">
                            <b><?=$_name?></b>
                            <ul class="list-unstyled">
                                <?
                                foreach ($_cat as $_page_name=>$_page_index) {
                                    ?>
                                    <label><input type="checkbox" name="role[]"<? if (@in_array($_page_index, $_POST["role"])!==FALSE) echo " checked"; ?> value="<?=$_page_index?>"> <?=$_page_name?>
                                    </label><br>
                                    <?
                                }
                                ?>
                            </ul>
                        </div>
                        <?
                    }
                    ?>
                </div>
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
                <h2>Пользователи панели управления</h2>
            </div>
            <div class="text-right row-phone">
                <a href="./?c=<?=$c?>&amp;action=edit_user" title="Добавить пользователя" class="btn btn-phone btn-primary">Добавить пользователя</a>
            </div>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Логин</th>
                    <th>Имя</th>
                    <th>Последний вход</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?
                $query=@mysqli_query($mysql, "SELECT * FROM `admin_users` WHERE 1 AND `id`<>'1' ORDER BY `login` ASC");
                while ($row=@mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?=@$row["login"]?></td>
                        <td><?=@$row["name"]?></td>
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
