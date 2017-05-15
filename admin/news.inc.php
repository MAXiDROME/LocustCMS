<?
//новости


if (isset($_GET["action"]) && $_GET["action"]=="delete_news") {//удаляем новость
    $query=@mysqli_query($mysql, "select * from `news` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    $row=@mysqli_fetch_assoc($query);
    if($row["filename"]!='')unlink("..".$newspath.@$row["filename"]);
    @mysqli_query($mysql, "delete from `news` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
    //удалили страницу
}
if (isset($_GET["action"]) && $_GET["action"]=="delete_photo") {//удаляем фото
    $query=@mysqli_query($mysql, "select * from `news` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    $row=@mysqli_fetch_assoc($query);
    if($row["filename"]!='')unlink("..".$newspath.@$row["filename"]);
    @mysqli_query($mysql, "update `news` set `filename`='' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    $_GET["action"]="edit_news";
    //удалили страницу
}
if (isset($_GET["action"]) && $_GET["action"]=="published") {
    @mysqli_query($mysql, "update `news` set `published`='1' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="draft") {
    @mysqli_query($mysql, "update `news` set `published`='0' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="show") {
    @mysqli_query($mysql, "update `news` set `visible`='1' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="hide") {
    @mysqli_query($mysql, "update `news` set `visible`='0' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}

if (isset($_POST["edit_news_and_exit"])) {

    $_GET["action"]=$_POST["action"];
    $_GET["id"]=$_POST["id"];
    $_GET["pid"]=$_POST["pid"];

    array_walk($_POST, 'makesafesqlstring');

    if ($_POST["title"]=="") $error["title"]="Обязательное поле";
    if ($_POST["rewrite"]!="") {
        $query=@mysqli_query($mysql, "select * from `news` where `pid`='".$_POST["pid"]."' and `rewrite`='" . $_POST["rewrite"] . "'");
        if ($row=@mysqli_fetch_assoc($query)) {
            if ($_GET["id"]=="" || ($_GET["id"]>0 && @$row["id"]!=$_GET["id"])) {
                $error["rewrite"]="Такой ЧПУ уже занят";
            }
        }
    }

    if (!isset($error)) {

        if($_POST["adddate"]=='')$_POST["adddate"]=@date("Y-m-d");

        if ($_GET["id"]>0) {
            @mysqli_query($mysql,"update `news` set 
                                                    `adddate`='".@date("Y-m-d 00:00:00",@strtotime($_POST["adddate"]))."',
                                                    `pid`='".$_POST["pid"]."',
                                                    `title`='".$_POST["title"]."',
                                                    `info`='".@$_POST["info"]."',
                                                    `content`='".$_POST["content"]."',
                                                    `meta_keywords`='".$_POST["meta_keywords"]."',
                                                    `meta_description`='".$_POST["meta_description"]."',
                                                    `meta_title`='".$_POST["meta_title"]."',
                                                    `visible`='".$_POST["visible"]."',
                                                    `published`='".$_POST["published"]."' 
                                          where `id`='".$_GET["id"]."'");
        } else {//новая страница
            @mysqli_query($mysql,"insert into `news` set 
                                                    `adddate`='".@date("Y-m-d 00:00:00",@strtotime($_POST["adddate"]))."',
                                                    `pid`='".$_POST["pid"]."',
                                                    `title`='".$_POST["title"]."',
                                                    `info`='".@$_POST["info"]."',
                                                    `content`='".$_POST["content"]."',
                                                    `meta_keywords`='".$_POST["meta_keywords"]."',
                                                    `meta_description`='".$_POST["meta_description"]."',
                                                    `meta_title`='".$_POST["meta_title"]."',
                                                    `visible`='".$_POST["visible"]."',
                                                    `published`='".$_POST["published"]."'");
            $_GET["id"]=@mysqli_insert_id($mysql);
        }

            if ($_POST["rewrite"]=="") {
                $_POST["rewrite"]=@makevalidurl($_POST["title"]);
                $i="";
                while (TRUE) {
                    $query=@mysqli_query($mysql, "select * from `news` where `pid`='".$_POST["pid"]."' and `rewrite`='" . $_POST["rewrite"].$i . "' and `id`<>'" . $_GET["id"] . "'");
                    if (mysqli_num_rows($query)) {
                        if ($i=="") $i=0;
                        $i++;
                    } else {
                        break;
                    }
                }
                $_POST["rewrite"]=$_POST["rewrite"] . $i;
            }
            @mysqli_query($mysql, "update `news` set `rewrite`='" . $_POST["rewrite"] . "' where `id`='" . $_GET["id"] . "'");

        //загрузка картинки
        /* todo: сделать загрузку картинок */
        if(isset($_FILES["photofile"]) && $_FILES["photofile"]["size"]>0){
            $query=@mysqli_query($mysql,"select * from `news` where `id`='".$_GET["id"]."'");
            $row=@mysqli_fetch_assoc($query);
            @unlink("..".@$newspath.@$row["filename"]);
            $filename=$_GET["id"]."-".makevalidurl($_FILES["photofile"]["name"]);
            //makethumb($_FILES["photofile"]["tmp_name"] , "..".$newspath.$filename);
            move_uploaded_file($_FILES["photofile"]["tmp_name"],"..".$newspath.$filename);
            mysqli_query($mysql,"update `news` set `filename`='".$filename."' where `id`='".$_GET["id"]."'");
        }
        //загрузили картинку

        $pid=$_GET["pid"];
        unset($_POST);
        unset($_GET);
        $_GET["pid"]=$pid;
        unset($pid);
    }
}

if (isset($_GET["action"]) && $_GET["action"]=="edit_news") {//добавляем-редактируем
    if (isset($_GET["id"]) && !isset($_POST["edit_news_and_exit"])) {
        $query=@mysqli_query($mysql, "select * from `news` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
        $_POST=@mysqli_fetch_assoc($query);
    }

    if (!isset($_POST["visible"])) $_POST["visible"]=1;
    if (!isset($_POST["published"])) $_POST["published"]=1;
    if(!isset($_POST["adddate"]))$_POST["adddate"]=@date("Y-m-d");

    if (isset($_GET["pid"])) $_POST["pid"]=$_GET["pid"];

    array_walk($_POST, 'makesafeformstring');

    ?>
    <div class="vertical-align m-b-1">
        <div class="text-left row-phone">
            <h2><? if (!isset($_GET["id"])) {
                    echo "Создание новости";
                } else {
                    echo "Редактирование новости";
                } ?></h2>
        </div>
    </div>


    <form action="." method="post" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="15000000">
        <input type="hidden" name="c" value="<?=$c?>">
        <input type="hidden" name="action" value="<?=$_GET["action"]?>">
        <input type="hidden" name="id" value="<?=$_GET["id"]?>">
        <input type="hidden" name="pid" value="<?=$_POST["pid"]?>">
        <input type="hidden" name="photo" value="<?=$_POST["filename"]?>">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#page" data-toggle="tab">Новость</a></li>
            <li><a href="#settings" data-toggle="tab">Настройки</a></li>
        </ul>

        <div class="tab-content tab-page m-b-1">
            <div class="tab-pane active" id="page">
                <div class="form-group<? if (isset($error["title"])) echo " has-error"; ?>">
                    <label class="control-label" for="page_title">Название</label>
                    <input type="text" id="page_title" name="title" value="<?=$_POST["title"]?>" class="form-control">
                    <? if (isset($error["title"])) { ?><span class="help-block"><?=$error["title"]?></span><? } ?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="page_meta_title">Заголовок</label>
                    <input type="text" id="page_meta_title" name="meta_title" value="<?=$_POST["meta_title"]?>" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label" for="page_keywords">Ключевые слова</label>
                    <input type="text" id="page_keywords" name="meta_keywords" value="<?=$_POST["meta_keywords"]?>" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label" for="page_description">Описание</label>
                    <textarea id="page_description" name="meta_description" class="form-control"><?=$_POST["meta_description"]?></textarea>
                </div>
                <div class="form-group<? if (isset($error["info"])) echo " has-error"; ?>">
                    <label class="control-label" for="page_info">Анонс</label>
                    <textarea id="page_info" name="info" class="form-control"><?=$_POST["info"]?></textarea>
                    <? if (isset($error["info"])) { ?>
                        <span class="help-block"><?=$error["info"]?></span><? } ?>
                </div>
                <div class="form-group">
                    <textarea id="content" name="content" style="width:100%; height:320px;"><?=$_POST["content"]?></textarea>
                </div>
            </div>
            <div class="tab-pane " id="settings">
                <div class="form-group<? if (isset($error["adddate"])) echo " has-error"; ?>">
                    <label class="control-label" for="page_adddate">Дата</label>
                    <input type="text" id="page_adddate" name="adddate" value="<?=@date("d.m.Y",@strtotime($_POST["adddate"]))?>" class="form-control datepicker" data-date-format="dd.mm.yyyy">
                    <? if (isset($error["adddate"])) { ?><span class="help-block"><?=$error["adddate"]?></span><? } ?>
                </div>
                <div class="form-group<? if (isset($error["rewrite"])) echo " has-error"; ?>">
                    <label class="control-label" for="page_rewrite">Ссылка (ЧПУ)</label>
                    <input type="text" id="page_rewrite" name="rewrite" value="<?=$_POST["rewrite"]?>" class="form-control"<? if ($_POST["protected"]==1) echo " disabled"; ?>>
                    <? if (isset($error["rewrite"])) { ?>
                        <span class="help-block"><?=$error["rewrite"]?></span><? } ?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="status">Статус</label>
                    <select id="status" name="published" class="form-control">
                        <option value="1"<? if ($_POST["published"]=='1') echo " selected"; ?>>Опубликовано</option>
                        <option value="0"<? if ($_POST["published"]=='0') echo " selected"; ?>>Черновик</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label" for="access">Доступ</label>
                    <select id="access" name="visible" class="form-control">
                        <option value="1"<? if ($_POST["visible"]=='1') echo " selected"; ?>>Видна</option>
                        <option value="0"<? if ($_POST["visible"]=='0') echo " selected"; ?>>Скрыта</option>
                    </select>
                </div>
            </div>
        </div>
        <script>
            //CKFinder.setupCKEditor();
            CKFinder.setupCKEditor( null, '/admin/ckfinder/' );
            CKEDITOR.config.allowedContent = true;
            CKEDITOR.replace('content', {
                language: 'ru',
                height: '500',
            });
        </script>
        <div class="row m-b-1">
            <div class="col-xs-12">
                <label class="control-label" for="photo">Фотография к новости</label><br>
<?
if($_POST["photo"]!='') {
    ?>
    <img src="..<?=$newspath.$_POST["photo"]?>" alt="" class="img-fluid m-b-1">
    <a href="./?c=<?=@$c?>&amp;pid=<?=$_GET["pid"]?>&amp;id=<?=$_GET["id"]?>&amp;action=delete_photo" class="m-t-1 btn btn-danger" onclick="if(!confirm('Удалить фото?'))return false;">Удалить</a>
    <?
}
?>
                <input type="file" name="photofile" class="">
                <div><small>Рекомендуемый размер фото: <?=@$n_thumb_w?>x<?=@$n_thumb_h?> пикселей.</small></div>
            </div>
        </div>

        <div class="row m-t-2">
            <div class="col-sm-6">
                <input type="submit" id="edit_news_and_exit" name="edit_news_and_exit" value="Сохранить и выйти" class="btn btn-phone btn-primary">&nbsp;
                <a href="./?c=<?=$c?>&amp;pid=<?=@$_GET["pid"]?>" title="Отмена" class="btn btn-phone btn-default">Отмена</a>
            </div>
        </div>
    </form>


    <?
    //добавили-отредактировали

} else {//список новостей


    $query=@mysqli_query($mysql, "select * from `pages` where `template`='news' order by `title`");
    if (@mysqli_num_rows($query)) {
        ?>
        <div class="vertical-align m-b-1">
            <div class="text-left row-phone">
                <h2>Новости</h2>
            </div>
            <div class="text-right row-phone">
                <form method="get" action="." class="form-inline">
                    <input type="hidden" name="c" value="<?=@$c?>">
                    <select name="pid" class="form-control" onchange="forms[0].submit();">
                        <?
                        while ($row=@mysqli_fetch_assoc($query)) {
                            if (!isset($_GET["pid"])) $_GET["pid"]=@$row["id"];
                            ?>
                            <option value="<?=@$row["id"]?>"<? if (@$_GET["pid"]==@$row["id"]) echo " selected"; ?>><?=@$row["title"]?></option>
                            <?
                        }
                        ?>
                    </select>
                    <a href="./?c=<?=$c?>&amp;pid=<?=$_GET["pid"]?>&amp;action=edit_news" title="Создать новость" class="btn btn-phone btn-primary">Создать новость</a>
                </form>
            </div>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Заголовок</th>
                    <th>ЧПУ</th>
                    <th>Статус</th>
                    <th>Доступ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?
                $query=@mysqli_query($mysql, "select * from `news` where `pid`='" . @$_GET["pid"] . "' order by `adddate` desc");
                while ($row=@mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?=@date("d.m.Y", @strtotime(@$row["adddate"]))?></td>
                        <td><?=@$row["title"]?></td>
                        <td><?=@$row["rewrite"]?></td>
                        <td><? if (@$row["published"]==1) {
                                echo "Опубликована";
                            } else {
                                echo "Черновик";
                            } ?></td>
                        <td><? if (@$row["visible"]==1) {
                                echo "Видна";
                            } else {
                                echo "Скрыта";
                            } ?></td>
                        <td nowrap>
                            <div class="pull-right">
                                <div class="btn-group">
                                    <a href="./?c=<?=$c?>&amp;action=edit_news&amp;id=<?=@$row["id"]?>&amp;pid=<?=@$_GET["pid"]?>" class="btn btn-primary">Редактировать</a>
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li class="dropdown-header">Статус</li>
                                        <li>
                                            <a href="./?c=<?=$c?>&amp;action=published&amp;id=<?=@$row["id"]?>&amp;pid=<?=@$_GET["pid"]?>">
                                                Опубликована
                                                <? if (@$row["published"]==1) { ?>
                                                    <span class="glyphicon glyphicon-ok"></span>
                                                <? } ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="./?c=<?=$c?>&amp;action=draft&amp;id=<?=@$row["id"]?>&amp;pid=<?=@$_GET["pid"]?>">
                                                Черновик
                                                <? if (@$row["published"]==0) { ?>
                                                    <span class="glyphicon glyphicon-ok"></span>
                                                <? } ?>
                                            </a>
                                        </li>
                                        <li class="dropdown-header">Доступ</li>
                                        <li>
                                            <a href="./?c=<?=$c?>&amp;action=show&amp;id=<?=@$row["id"]?>&amp;pid=<?=@$_GET["pid"]?>">
                                                Видна
                                                <? if (@$row["visible"]==1) { ?>
                                                    <span class="glyphicon glyphicon-ok"></span>
                                                <? } ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="./?c=<?=$c?>&amp;action=hide&amp;id=<?=@$row["id"]?>&amp;pid=<?=@$_GET["pid"]?>">
                                                Скрыта
                                                <? if (@$row["visible"]==0) { ?>
                                                    <span class="glyphicon glyphicon-ok"></span>
                                                <? } ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <a href="./?c=<?=$c?>&amp;action=delete_news&amp;id=<?=@$row["id"]?>&amp;pid=<?=@$_GET["pid"]?>" class="btn btn-danger" onclick="return confirm('Удалить новость?')">Удалить</a>
                            </div>
                        </td>
                    </tr>
                    <?
                }
                ?>
            </tbody>
        </table>
        <?
    } else {
        ?>
        <div class="vertical-align m-b-1">
            <div class="text-left row-phone">
                <h2>Новости</h2>
            </div>
        </div>
        <h3>Сперва нужно создать страницу с новостями</h3>
        <?
    }

}
