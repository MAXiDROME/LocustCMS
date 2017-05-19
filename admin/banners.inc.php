<?
/**
 * Автор: Кореев Максим. (c) 2016.
 */

//баннеры

if (isset($_GET["action"]) && $_GET["action"]=="move-up") {
    $query=@mysqli_query($mysql,"select * from `banners` where `id`='".@mysqli_real_escape_string($mysql,$_GET["id"])."'");
    $row=@mysqli_fetch_assoc($query);
    $query=@mysqli_query($mysql,"select * from `banners` where `order`='".(@$row["order"]-1)."'");
    if($row1=@mysqli_fetch_assoc($query)){
        @mysqli_query($mysql,"update `banners` set `order`='".@$row1["order"]."' where `id`='".@$row["id"]."'");
        @mysqli_query($mysql,"update `banners` set `order`='".@$row["order"]."' where `id`='".@$row1["id"]."'");
    }
}
if (isset($_GET["action"]) && $_GET["action"]=="move-down") {
    $query=@mysqli_query($mysql,"select * from `banners` where `id`='".@mysqli_real_escape_string($mysql,$_GET["id"])."'");
    $row=@mysqli_fetch_assoc($query);
    $query=@mysqli_query($mysql,"select * from `banners` where `order`='".(@$row["order"]+1)."'");
    if($row1=@mysqli_fetch_assoc($query)){
        @mysqli_query($mysql,"update `banners` set `order`='".@$row1["order"]."' where `id`='".@$row["id"]."'");
        @mysqli_query($mysql,"update `banners` set `order`='".@$row["order"]."' where `id`='".@$row1["id"]."'");
    }
}

if (isset($_GET["action"]) && $_GET["action"]=="delete_banners") {//удаляем
    $query=@mysqli_query($mysql, "select * from `banners` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    $row=@mysqli_fetch_assoc($query);
    admin_log('Удаление баннера "'.$row["name"].'"');
    if($row["filename"]!='')unlink("..".$bannerspath.@$row["filename"]);
    @mysqli_query($mysql, "delete from `banners` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
    repair_banners_order();
    //удалили страницу
}
if (isset($_GET["action"]) && $_GET["action"]=="delete_photo") {//удаляем фото
    $query=@mysqli_query($mysql, "select * from `banners` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    $row=@mysqli_fetch_assoc($query);
    admin_log('Удаление изображения баннера "'.$row["name"].'"');
    if($row["filename"]!='')unlink("..".$bannerspath.@$row["filename"]);
    @mysqli_query($mysql, "update `banners` set `filename`='' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    $_GET["action"]="edit_banners";
    //удалили страницу
}
if (isset($_GET["action"]) && $_GET["action"]=="published") {
    @mysqli_query($mysql, "update `banners` set `published`='1' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="draft") {
    @mysqli_query($mysql, "update `banners` set `published`='0' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="show") {
    @mysqli_query($mysql, "update `banners` set `visible`='1' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="hide") {
    @mysqli_query($mysql, "update `banners` set `visible`='0' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}

if (isset($_POST["edit_banners_and_exit"])) {

    $_GET["action"]=$_POST["action"];
    $_GET["id"]=$_POST["id"];

    array_walk($_POST, 'makesafesqlstring');

    if ($_POST["name"]=="") $error["name"]="Обязательное поле";

    if (!isset($error)) {

        if ($_GET["id"]>0) {
            @mysqli_query($mysql,"update `banners` set `href`='".@$_POST["href"]."',`name`='".$_POST["name"]."',`text1`='".@$_POST["text1"]."',`text2`='".@$_POST["text2"]."' where `id`='".$_GET["id"]."'");
            admin_log('Изменение баннера "'.$_POST["name"].'"');
        } else {//новая страница
            @mysqli_query($mysql, "insert into `banners` (`href`,`name`,`text1`,`text2`,`order`) values ('".$_POST["href"]."','" . $_POST["name"] . "','".@$_POST["text1"]."','".@$_POST["text2"]."','9999')");
            admin_log('Добавление баннера "'.$row["name"].'"');
            $_GET["id"]=@mysqli_insert_id($mysql);
            repair_banners_order();
        }

        //загрузка картинки
        /* todo: сделать загрузку картинок */
        if(isset($_FILES["photofile"]) && $_FILES["photofile"]["size"]>0){
            $query=@mysqli_query($mysql,"select * from `banners` where `id`='".$_GET["id"]."'");
            $row=@mysqli_fetch_assoc($query);
            admin_log('Изменение изображения баннера "'.$row["name"].'"');
            @unlink("..".@$bannerspath.@$row["filename"]);
            $filename=$_GET["id"]."-".makevalidurl($_FILES["photofile"]["name"]);
            move_uploaded_file($_FILES["photofile"]["tmp_name"],"..".$bannerspath.$filename);
            mysqli_query($mysql,"update `banners` set `filename`='".$filename."' where `id`='".$_GET["id"]."'");
        }
        //загрузили картинку

        $pid=$_GET["pid"];
        unset($_POST);
        unset($_GET);
        $_GET["pid"]=$pid;
        unset($pid);
    }
}

if (isset($_GET["action"]) && $_GET["action"]=="edit_banners") {//добавляем-редактируем
    if (isset($_GET["id"]) && !isset($_POST["edit_banners_and_exit"])) {
        $query=@mysqli_query($mysql, "select * from `banners` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
        $_POST=@mysqli_fetch_assoc($query);
    }

    if (!isset($_POST["visible"])) $_POST["visible"]=1;

    array_walk($_POST, 'makesafeformstring');

    ?>
    <div class="vertical-align m-b-1">
        <div class="text-left row-phone">
            <h2><? if (!isset($_GET["id"])) {
                    echo "Создание баннера";
                } else {
                    echo "Редактирование баннера";
                } ?></h2>
        </div>
    </div>


    <form action="." method="post" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="15000000">
        <input type="hidden" name="c" value="<?=$c?>">
        <input type="hidden" name="action" value="<?=$_GET["action"]?>">
        <input type="hidden" name="id" value="<?=$_GET["id"]?>">
        <input type="hidden" name="picture" value="<?=$_POST["picture"]?>">

        <div class="form-group<? if (isset($error["name"])) echo " has-error"; ?>">
            <label class="control-label" for="page_name">Название</label>
            <input type="text" id="page_name" name="name" value="<?=$_POST["name"]?>" class="form-control">
            <? if (isset($error["name"])) { ?><span class="help-block"><?=$error["name"]?></span><? } ?>
        </div>
        <div class="form-group<? if (isset($error["href"])) echo " has-error"; ?>">
            <label class="control-label" for="page_info">Ссылка</label>
            <input type="text" id="page_info" name="href" class="form-control" value="<?=$_POST["href"]?>">
            <? if (isset($error["href"])) { ?>
                <span class="help-block"><?=$error["href"]?></span><? } ?>
        </div>
        <div class="tab-content tab-page m-b-1">
            <div class="tab-pane active" id="page">
                <div class="form-group<? if (isset($error["text1"])) echo " has-error"; ?>">
                    <label class="control-label" for="text1">Красный текст</label>
                    <input type="text" id="text1" name="text1" class="form-control" value="<?=$_POST["text1"]?>">
                    <? if (isset($error["text1"])) { ?>
                        <span class="help-block"><?=$error["text1"]?></span><? } ?>
                </div>
                <div class="form-group<? if (isset($error["text2"])) echo " has-error"; ?>">
                    <label class="control-label" for="text2">Белый текст</label>
                    <input type="text" id="text2" name="text2" class="form-control" value="<?=$_POST["text2"]?>">
                    <? if (isset($error["text2"])) { ?>
                        <span class="help-block"><?=$error["text2"]?></span><? } ?>
                </div>
            </div>
        </div>

        <div class="row m-b-1">
            <div class="col-xs-12">
                <label class="control-label" for="photo">Фотография</label><br>
                <?
                if($_POST["filename"]!='') {
                    ?>
                    <img src="..<?=$bannerspath.$_POST["filename"]?>" alt="" class="img-fluid m-b-1">
                    <a href="./?c=<?=@$c?>&amp;id=<?=$_GET["id"]?>&amp;action=delete_photo" class="m-t-1 btn btn-danger" onclick="if(!confirm('Удалить фото?'))return false;">Удалить</a>
                    <?
                }
                ?>
                <input type="file" name="photofile" class="">
                <div><small>Рекомендуемый размер баннера: <?=@$b_thumb_w?>x<?=@$b_thumb_h?> пикселей.</small></div>
            </div>
        </div>

        <div class="row m-t-2">
            <div class="col-sm-6">
                <input type="submit" id="edit_banners_and_exit" name="edit_banners_and_exit" value="Сохранить и выйти" class="btn btn-phone btn-primary">&nbsp;
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
            <h2>Баннеры</h2>
        </div>
        <div class="text-right row-phone">
            <a href="./?c=<?=$c?>&amp;action=edit_banners" title="Создать баннер" class="btn btn-phone btn-primary">Создать баннер</a>
        </div>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Заголовок</th>
                <th>Изображение</th>
                <th>Доступ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?
            $query=@mysqli_query($mysql, "select * from `banners` where 1 order by `order` asc");
            while ($row=@mysqli_fetch_assoc($query)) {
                ?>
                <tr>
                    <td>
                        <a href="./?c=<?=$c?>&amp;action=move-up&amp;id=<?=@$row["id"]?>" title="Выше"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a href="./?c=<?=$c?>&amp;action=move-down&amp;id=<?=@$row["id"]?>" title="Ниже"><span class="glyphicon glyphicon-arrow-down"></span></a>
                        <?=@$row["name"]?>
                    </td>
                    <td><img src="<?=@$bannerspath.@$row["filename"]?>" class="" alt="" height="80px"></td>
                    <td><? if (@$row["visible"]==1) {
                            echo "Виден";
                        } else {
                            echo "Скрыт";
                        } ?></td>
                    <td nowrap>
                        <div class="pull-right">
                            <div class="btn-group">
                                <a href="./?c=<?=$c?>&amp;action=edit_banners&amp;id=<?=@$row["id"]?>&amp;pid=<?=@$_GET["pid"]?>" class="btn btn-primary">Редактировать</a>
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li class="dropdown-header">Доступ</li>
                                    <li>
                                        <a href="./?c=<?=$c?>&amp;action=show&amp;id=<?=@$row["id"]?>&amp;pid=<?=@$_GET["pid"]?>">
                                            Виден
                                            <? if (@$row["visible"]==1) { ?>
                                                <span class="glyphicon glyphicon-ok"></span>
                                            <? } ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="./?c=<?=$c?>&amp;action=hide&amp;id=<?=@$row["id"]?>&amp;pid=<?=@$_GET["pid"]?>">
                                            Скрыт
                                            <? if (@$row["visible"]==0) { ?>
                                                <span class="glyphicon glyphicon-ok"></span>
                                            <? } ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <a href="./?c=<?=$c?>&amp;action=delete_banners&amp;id=<?=@$row["id"]?>" class="btn btn-danger" onclick="return confirm('Удалить баннер?')">Удалить</a>
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


function repair_banners_order(){
    global $mysql;

    $i=0;
    $query=@mysqli_query($mysql,"select * from `banners` order by `order` asc");
    while ($row=@mysqli_fetch_assoc($query)){
        @mysqli_query($mysql,"update `banners` set `order`='$i' where `id`='".@$row["id"]."'");
        $i++;
    }
}