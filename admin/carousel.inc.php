<?
if (isset($_GET["action"]) && $_GET["action"]=="delete_carousel") {//удаляем
    $query=@mysqli_query($mysql,"select * from `carousel` where `id`='".@mysqli_real_escape_string($mysql,$_GET["id"])."'");
    $row=@mysqli_fetch_assoc($query);
    admin_log('Удаление элемента карусели "'.$row["title"].'"');
    @mysqli_query($mysql, "delete from `carousel` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    repaircarouselorder();

    unset($_GET["action"]);
    unset($_GET["id"]);
    //удалили
}
if (isset($_GET["action"]) && $_GET["action"]=="show") {
    @mysqli_query($mysql, "update `carousel` set `visible`='1' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="hide") {
    @mysqli_query($mysql, "update `carousel` set `visible`='0' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="move-up") {
    $query=@mysqli_query($mysql,"select * from `carousel` where `id`='".@mysqli_real_escape_string($mysql,$_GET["id"])."'");
    $row=@mysqli_fetch_assoc($query);
    $query=@mysqli_query($mysql,"select * from `carousel` where `order`='".(@$row["order"]-1)."'");
    if($row1=@mysqli_fetch_assoc($query)){
        @mysqli_query($mysql,"update `carousel` set `order`='".@$row1["order"]."' where `id`='".@$row["id"]."'");
        @mysqli_query($mysql,"update `carousel` set `order`='".@$row["order"]."' where `id`='".@$row1["id"]."'");
    }
}
if (isset($_GET["action"]) && $_GET["action"]=="move-down") {
    $query=@mysqli_query($mysql,"select * from `carousel` where `id`='".@mysqli_real_escape_string($mysql,$_GET["id"])."'");
    $row=@mysqli_fetch_assoc($query);
    $query=@mysqli_query($mysql,"select * from `carousel` where `order`='".(@$row["order"]+1)."'");
    if($row1=@mysqli_fetch_assoc($query)){
        @mysqli_query($mysql,"update `carousel` set `order`='".@$row1["order"]."' where `id`='".@$row["id"]."'");
        @mysqli_query($mysql,"update `carousel` set `order`='".@$row["order"]."' where `id`='".@$row1["id"]."'");
    }
}

if (isset($_POST["edit_page_and_exit"])) {

    $_GET["action"]=$_POST["action"];
    $_GET["id"]=$_POST["id"];

    array_walk($_POST, 'makesafesqlstring');

    if ($_POST["title"]=="") $error["title"]="Обязательное поле";

    if (!isset($error)) {

        if ($_GET["id"]>0) {
            @mysqli_query($mysql,"update `carousel` set 
                                                      `title`='".$_POST["title"]."',
                                                      `content`='".$_POST["content"]."',
                                                      `visible`='".$_POST["visible"]."'
                                                  where `id`='".$_GET["id"]."'");
            admin_log('Изменение элемента карусели "'.$_POST["title"].'"');

        } else {//новая страница
            @mysqli_query($mysql,"insert into `carousel` set 
                                                      `order`='9999',
                                                      `title`='".$_POST["title"]."',
                                                      `content`='".$_POST["content"]."',
                                                      `visible`='".$_POST["visible"]."'
                                                  ");
            admin_log('Изменение элемента карусели "'.$_POST["title"].'"');
            $_GET["id"]=@mysqli_insert_id($mysql);
            repaircarouselorder();
        }

        unset($_POST);
        unset($_GET);
    }
}

if (isset($_GET["action"]) && $_GET["action"]=="edit_page") {//добавляем-редактируем
    if (isset($_GET["id"]) && !isset($_POST["edit_page_and_exit"])) {
        $query=@mysqli_query($mysql, "select * from `carousel` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
        $_POST=@mysqli_fetch_assoc($query);
    }

    if (!isset($_POST["visible"])) $_POST["visible"]=1;

    array_walk($_POST, 'makesafeformstring');

    ?>
    <div class="vertical-align margin-bottom-1">
        <div class="text-left row-phone">
            <h2><? if (!isset($_GET["id"])) {
                    echo "Новый элемент";
                } else {
                    echo "Редактирование элемента";
                } ?></h2>
        </div>
    </div>


    <form action="." method="post">
        <input type="hidden" name="c" value="<?=$c?>">
        <input type="hidden" name="action" value="<?=$_GET["action"]?>">
        <input type="hidden" name="id" value="<?=$_GET["id"]?>">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#page" data-toggle="tab">Элемент</a></li>
            <li><a href="#settings" data-toggle="tab">Настройки</a></li>
        </ul>

        <div class="tab-content tab-page margin-bottom-1">
            <div class="tab-pane active" id="page">
                <div class="form-group<? if (isset($error["title"])) echo " has-error"; ?>">
                    <label class="control-label" for="page_title">Заголовок</label>
                    <input type="text" id="page_title" name="title" value="<?=$_POST["title"]?>" class="form-control">
                    <? if (isset($error["title"])) { ?><span class="help-block"><?=$error["title"]?></span><? } ?>
                </div>
                <div class="form-group">
                    <textarea id="content" name="content" class="form-control"><?=$_POST["content"]?></textarea>
                </div>
            </div>
            <div class="tab-pane" id="settings">
                <div class="form-group">
                    <label class="control-label" for="access">Доступ</label>
                    <select id="access" name="visible" class="form-control">
                        <option value="1"<? if ($_POST["visible"]=='1') echo " selected"; ?>>Виден</option>
                        <option value="0"<? if ($_POST["visible"]=='0') echo " selected"; ?>>Скрыт</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row margin-top-1">
            <div class="col-sm-6">
                <input type="submit" id="edit_page_and_exit" name="edit_page_and_exit" value="Сохранить и выйти" class="btn btn-phone btn-primary">&nbsp;
                <a href="./?c=<?=$c?>" title="Отмена" class="btn btn-phone btn-default">Отмена</a>
            </div>
        </div>
    </form>


    <?
    //добавили-отредактировали

} else {
    ?>
    <div class="vertical-align margin-bottom-1">
        <div class="text-left row-phone">
            <h2>Карусель на главной странице</h2>
        </div>
        <div class="text-right row-phone">
            <a href="./?c=<?=$c?>&amp;action=edit_page" title="Создать новую элемент"
               class="btn btn-phone btn-primary">Создать новый элемент</a>
        </div>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Название</th>
                <th>Текст</th>
                <th>Доступ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?
            $query=@mysqli_query($mysql, "select * from `carousel` where 1 order by `order` asc");
            while ($row=@mysqli_fetch_assoc($query)) {
                ?>
                <tr>
                    <td>
                        <a href="./?c=<?=$c?>&amp;action=move-up&amp;id=<?=@$row["id"]?>" title="Выше"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a href="./?c=<?=$c?>&amp;action=move-down&amp;id=<?=@$row["id"]?>" title="Ниже"><span class="glyphicon glyphicon-arrow-down"></span></a>
                        <?=@$row["title"]?>
                    </td>
                    <td><?=@$row["content"]?></td>
                    <td><? if (@$row["visible"]==1) {
                            echo "Виден";
                        } else {
                            echo "Скрыт";
                        } ?></td>
                    <td nowrap>
                        <div class="pull-right">
                            <div class="btn-group">
                                <a href="./?c=<?=$c?>&amp;action=edit_page&amp;id=<?=@$row["id"]?>" class="btn btn-primary">Редактировать</a>
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li class="dropdown-header">Доступ</li>
                                    <li>
                                        <a href="./?c=<?=$c?>&amp;action=show&amp;id=<?=@$row["id"]?>">
                                            Виден
                                            <? if (@$row["visible"]==1) { ?>
                                                <span class="glyphicon glyphicon-ok"></span>
                                            <? } ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="./?c=<?=$c?>&amp;action=hide&amp;id=<?=@$row["id"]?>">
                                            Скрыт
                                            <? if (@$row["visible"]==0) { ?>
                                                <span class="glyphicon glyphicon-ok"></span>
                                            <? } ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <a href="./?c=<?=$c?>&amp;action=delete_carousel&amp;id=<?=@$row["id"]?>" class="btn btn-danger" onclick="return confirm('Удалить элемент?')">Удалить</a>
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


function repaircarouselorder () {
    global $mysql;

    $i=0;
    $query=@mysqli_query($mysql, "select * from `carousel` where 1 order by `order` asc");
    while ($row=@mysqli_fetch_assoc($query)) {
        @mysqli_query($mysql, "update `carousel` set `order`='" . $i . "' where `id`='" . @$row["id"] . "'");
        $i++;
    }
}