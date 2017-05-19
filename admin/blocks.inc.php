<?php
//блоки

if (isset($_GET["action"]) && $_GET["action"]=="delete") {//удаляем
    $query=@mysqli_query($mysql,"select * from `blocks` where `id`='".@mysqli_real_escape_string($mysql,$_GET["id"])."'");
    $row=@mysqli_fetch_assoc($query);
    admin_log('Удаление блока "'.$row["name"].'"');
    @mysqli_query($mysql, "DELETE FROM `blocks` WHERE `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_POST["edit_block_and_exit"])) {

    $_GET["action"]=$_POST["action"];
    $_GET["id"]=$_POST["id"];

    array_walk($_POST, 'makesafesqlstring');

    if ($_POST["name"]=="") $error["name"]="Обязательное поле";

    if (!isset($error)) {

        if ($_GET["id"]>0) {
            @mysqli_query($mysql, "UPDATE `blocks` SET `name`='" . $_POST["name"] . "',`comment`='" . @$_POST["comment"] . "',`content`='" . @$_POST["content"] . "' WHERE `id`='" . $_GET["id"] . "'");
            admin_log('Изменение блока "'.$_POST["name"].'"');
        } else {//новая страница
            @mysqli_query($mysql, "INSERT INTO `blocks` (`name`,`comment`,`content`) VALUES ('" . $_POST["name"] . "','" . @$_POST["comment"] . "','" . @$_POST["content"] . "')");
            admin_log('Добавление блока "'.$_POST["name"].'"');
            $_GET["id"]=@mysqli_insert_id($mysql);
        }

        unset($_POST);
        unset($_GET);
    }
}

if (isset($_GET["action"]) && $_GET["action"]=="edit_block") {//добавляем-редактируем
    if (isset($_GET["id"]) && !isset($_POST["edit_block_and_exit"])) {
        $query=@mysqli_query($mysql, "SELECT * FROM `blocks` WHERE `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
        $_POST=@mysqli_fetch_assoc($query);
    }

    array_walk($_POST, 'makesafeformstring');

    ?>
    <div class="vertical-align m-b-1">
        <div class="text-left row-phone">
            <h2><? if (!isset($_GET["id"])) {
                    echo "Создание блока";
                } else {
                    echo "Редактирование блока";
                } ?></h2>
        </div>
    </div>


    <form action="." method="post">
        <input type="hidden" name="c" value="<?=$c?>"> <input type="hidden" name="action" value="<?=$_GET["action"]?>">
        <input type="hidden" name="id" value="<?=$_GET["id"]?>">

        <div class="form-group<? if (isset($error["name"])) echo " has-error"; ?>">
            <label class="control-label" for="page_name">Название</label>
            <input type="text" id="page_name" name="name" value="<?=$_POST["name"]?>" class="form-control">
            <? if (isset($error["name"])) { ?><span class="help-block"><?=$error["name"]?></span><? } ?>
        </div>
        <div class="form-group<? if (isset($error["comment"])) echo " has-error"; ?>">
            <label class="control-label" for="page_info">Описание</label>
            <textarea id="page_info" name="comment" class="form-control"><?=$_POST["comment"]?></textarea>
            <? if (isset($error["comment"])) { ?>
                <span class="help-block"><?=$error["comment"]?></span><? } ?>
        </div>
        <div class="form-group<? if (isset($error["content"])) echo " has-error"; ?>">
            <label class="control-label" for="content">Код</label>
            <textarea id="content" name="content" class="form-control"><?=$_POST["content"]?></textarea>
            <? if (isset($error["content"])) { ?>
                <span class="help-block"><?=$error["content"]?></span><? } ?>
        </div>

        <div class="row m-t-2">
            <div class="col-sm-6">
                <input type="submit" id="edit_block_and_exit" name="edit_block_and_exit" value="Сохранить и выйти" class="btn btn-phone btn-primary">&nbsp;
                <a href="./?c=<?=$c?>" title="Отмена" class="btn btn-phone btn-default">Отмена</a>
            </div>
        </div>
    </form>


    <?
    //добавили-отредактировали

} else {//список программ
    ?>
    <div class="vertical-align m-b-1">
        <div class="text-left row-phone">
            <h2>Блоки</h2>
        </div>
        <div class="text-right row-phone">
            <a href="./?c=<?=$c?>&amp;action=edit_block" title="Создать блок" class="btn btn-phone btn-primary">Создать блок</a>
        </div>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Имя блока</th>
                <th>Описание</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?
            $query=@mysqli_query($mysql, "SELECT * FROM `blocks` WHERE 1 ORDER BY `name` ASC");
            while ($row=@mysqli_fetch_assoc($query)) {
                ?>
                <tr>
                    <td>
                        <?=@$row["name"]?>
                    </td>
                    <td><?=nl2br(@$row["comment"])?></td>
                    <td nowrap>
                        <div class="pull-right">
                            <div class="btn-group">
                                <a href="./?c=<?=$c?>&amp;action=edit_block&amp;id=<?=@$row["id"]?>&amp;pid=<?=@$_GET["pid"]?>" class="btn btn-primary">Редактировать</a>
                            </div>
                            <a href="./?c=<?=$c?>&amp;action=delete_block&amp;id=<?=@$row["id"]?>" class="btn btn-danger" onclick="return confirm('Удалить блок?')">Удалить</a>
                        </div>
                    </td>
                </tr>
                <?
            }
            ?>
        </tbody>
    </table>
    <?
}
