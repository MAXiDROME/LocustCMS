<?
if (isset($_GET["action"]) && $_GET["action"]=="delete_page") {//удаляем страницу
    $query=@mysqli_query($mysql, "select * from `pages` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    $row=@mysqli_fetch_assoc($query);
    $pid=@$row["pid"];
    if ($row["protected"]==0) {
        $query=@mysqli_query($mysql, "select * from `pages` where `pid`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
        while ($row=@mysqli_fetch_assoc($query)) {
            if (@$row["protected"]==0) {
                @mysqli_query($mysql, "delete from `pages` where `id`='" . @$row["id"] . "'");
                @mysqli_query($mysql, "delete from `news` where `pid`='" . @$row["id"] . "'");
            } else {
                @mysqli_query($mysql, "update `pages` set `visible`='0',`order`='9999',`pid`='0' where `id`='" . @$row["id"] . "'");
            }
        }
        @mysqli_query($mysql, "delete from `pages` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
        @mysqli_query($mysql, "delete from `news` where `pid`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");

        repairpagesorder($pid);
        unset($pid);

    }
    unset($_GET["action"]);
    unset($_GET["id"]);
    //удалили страницу
}
if (isset($_GET["action"]) && $_GET["action"]=="published") {
    @mysqli_query($mysql, "update `pages` set `published`='1' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="draft") {
    @mysqli_query($mysql, "update `pages` set `published`='0' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="show") {
    @mysqli_query($mysql, "update `pages` set `visible`='1' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="hide") {
    @mysqli_query($mysql, "update `pages` set `visible`='0' where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
    unset($_GET["action"]);
    unset($_GET["id"]);
}
if(isset($_GET["action"]) && $_GET["action"]=="clone_page"){

    $query=@mysqli_query($mysql,"select * from `pages` where `id`='".@mysqli_real_escape_string($mysql,$_GET["id"])."'");
    $row=@mysqli_fetch_assoc($query);

    if(@$row["protected"]!="") {

        $sql="insert into `pages` set";
        foreach ($row as $key=>$val) {
            if ($key!="id" && $key!="order") $keyarr[]="`" . $key . "`='" . $val . "'";
        }
        $keyarr[]="`order`='9999'";


        $rewrite=@makevalidurl(@$row["title"]);
        $i="";
        while (TRUE) {
            $query=@mysqli_query($mysql, "select * from `pages` where `rewrite`='" . $rewrite.$i . "/'");
            if (mysqli_num_rows($query)) {
                if ($i=="") $i=0;
                $i++;
            } else {
                break;
            }
        }
        $keyarr[]="`rewrite`='".$rewrite."/'";;

        $sql.=@join(",", $keyarr);

        @mysqli_query($mysql, $sql);

        repairpagesorder(@$row["pid"]);
    }

    unset($_GET["action"]);
    unset($_GET["id"]);
}
if (isset($_GET["action"]) && $_GET["action"]=="move-up") {
    $query=@mysqli_query($mysql,"select * from `pages` where `id`='".@mysqli_real_escape_string($mysql,$_GET["id"])."'");
    $row=@mysqli_fetch_assoc($query);
    $query=@mysqli_query($mysql,"select * from `pages` where `pid`='".@$row["pid"]."' and `order`='".(@$row["order"]-1)."'");
    if($row1=@mysqli_fetch_assoc($query)){
        @mysqli_query($mysql,"update `pages` set `order`='".@$row1["order"]."' where `id`='".@$row["id"]."'");
        @mysqli_query($mysql,"update `pages` set `order`='".@$row["order"]."' where `id`='".@$row1["id"]."'");
    }
}
if (isset($_GET["action"]) && $_GET["action"]=="move-down") {
    $query=@mysqli_query($mysql,"select * from `pages` where `id`='".@mysqli_real_escape_string($mysql,$_GET["id"])."'");
    $row=@mysqli_fetch_assoc($query);
    $query=@mysqli_query($mysql,"select * from `pages` where `pid`='".@$row["pid"]."' and `order`='".(@$row["order"]+1)."'");
    if($row1=@mysqli_fetch_assoc($query)){
        @mysqli_query($mysql,"update `pages` set `order`='".@$row1["order"]."' where `id`='".@$row["id"]."'");
        @mysqli_query($mysql,"update `pages` set `order`='".@$row["order"]."' where `id`='".@$row1["id"]."'");
    }
}

if (isset($_POST["edit_page_and_exit"])) {

    $_GET["action"]=$_POST["action"];
    $_GET["id"]=$_POST["id"];

    array_walk($_POST, 'makesafesqlstring');

    if ($_POST["title"]=="") $error["title"]="Обязательное поле";
    if ($_POST["rewrite"]!="") {
        $query=@mysqli_query($mysql, "select * from `pages` where `rewrite`='" . $_POST["rewrite"] . "/'");
        if ($row=@mysqli_fetch_assoc($query)) {
            if ($_GET["id"]=="" || ($_GET["id"]>0 && @$row["id"]!=$_GET["id"])) {
                $error["rewrite"]="Такой ЧПУ уже занят";
            }
        }
    }

    if (!isset($error)) {

        if ($_GET["id"]>0) {
            if($_POST["pid"]!=$_POST["oldpid"])@mysqli_query($mysql,"update `pages` set `order`='9999' where `id`='".$_GET["id"]."'");
            @mysqli_query($mysql,"update `pages` set 
                                                      `pid`='".$_POST["pid"]."',
                                                      `title`='".$_POST["title"]."',
                                                      `meta_keywords`='".$_POST["meta_keywords"]."',
                                                      `meta_description`='".$_POST["meta_description"]."',
                                                      `meta_title`='".$_POST["meta_title"]."',
                                                      `content`='".$_POST["content"]."',
                                                      `visible`='".$_POST["visible"]."',
                                                      `published`='".$_POST["published"]."' 
                                                  where `id`='".$_GET["id"]."'");
            repairpagesorder($_POST["pid"]);
            repairpagesorder($_POST["oldpid"]);

        } else {//новая страница
            @mysqli_query($mysql,"insert into `pages` set 
                                                      `order`='9999',
                                                      `pid`='".$_POST["pid"]."',
                                                      `title`='".$_POST["title"]."',
                                                      `meta_keywords`='".$_POST["meta_keywords"]."',
                                                      `meta_description`='".$_POST["meta_description"]."',
                                                      `meta_title`='".$_POST["meta_title"]."',
                                                      `content`='".$_POST["content"]."',
                                                      `visible`='".$_POST["visible"]."',
                                                      `published`='".$_POST["published"]."'");
            $_GET["id"]=@mysqli_insert_id($mysql);
            repairpagesorder($_POST["pid"]);
        }

        if ($_POST["protected"]==0) {
            @mysqli_query($mysql, "update `pages` set `template`='" . $_POST["template"] . "' where `id`='" . $_GET["id"] . "'");

            if ($_POST["rewrite"]=="") {
                $_POST["rewrite"]=@makevalidurl($_POST["title"]);
                $i="";
                while (TRUE) {
                    $query=@mysqli_query($mysql, "select * from `pages` where `rewrite`='" . $_POST["rewrite"].$i . "/' and `id`<>'" . $_GET["id"] . "'");
                    if (mysqli_num_rows($query)) {
                        if ($i=="") $i=0;
                        $i++;
                    } else {
                        break;
                    }
                }
                $_POST["rewrite"]=$_POST["rewrite"] . $i;
            }
            @mysqli_query($mysql, "update `pages` set `rewrite`='" . $_POST["rewrite"] . "/' where `id`='" . $_GET["id"] . "'");

        }

        unset($_POST);
        unset($_GET);
    }
}

if (isset($_GET["action"]) && $_GET["action"]=="edit_page") {//добавляем-редактируем
    if (isset($_GET["id"]) && !isset($_POST["edit_page_and_exit"])) {
        $query=@mysqli_query($mysql, "select * from `pages` where `id`='" . @mysqli_real_escape_string($mysql, $_GET["id"]) . "'");
        $_POST=@mysqli_fetch_assoc($query);
        $_POST["rewrite"]=@substr($_POST["rewrite"],0,-1);
        $_POST["oldpid"]=@$_POST["pid"];
    }

    if (!isset($_POST["visible"])) $_POST["visible"]=1;
    if (!isset($_POST["published"])) $_POST["published"]=1;

    if (isset($_GET["pid"])) $_POST["pid"]=$_GET["pid"];

    array_walk($_POST, 'makesafeformstring');

    ?>
    <div class="vertical-align margin-bottom-1">
        <div class="text-left row-phone">
            <h2><? if (!isset($_GET["id"])) {
                    echo "Новая страница";
                } else {
                    echo "Редактирование страницы";
                } ?></h2>
        </div>
    </div>


    <form action="." method="post">
        <input type="hidden" name="c" value="<?=$c?>">
        <input type="hidden" name="action" value="<?=$_GET["action"]?>">
        <input type="hidden" name="id" value="<?=$_GET["id"]?>">
        <input type="hidden" name="oldpid" value="<?=$_POST["oldpid"]?>">
        <input type="hidden" name="protected" value="<?=$_POST["protected"]?>">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#page" data-toggle="tab">Страница</a></li>
            <li><a href="#settings" data-toggle="tab">Настройки</a></li>
        </ul>

        <div class="tab-content tab-page margin-bottom-1">
            <div class="tab-pane active" id="page">
                <div class="form-group<? if (isset($error["title"])) echo " has-error"; ?>">
                    <label class="control-label" for="page_title">Название страницы</label>
                    <input type="text" id="page_title" name="title" value="<?=$_POST["title"]?>" class="form-control">
                    <? if (isset($error["title"])) { ?><span class="help-block"><?=$error["title"]?></span><? } ?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="page_meta_title">Мета заголовок</label>
                    <input type="text" id="page_meta_title" name="meta_title" value="<?=$_POST["meta_title"]?>" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label" for="page_keywords">Мета ключевые слова</label>
                    <input type="text" id="page_keywords" name="meta_keywords" value="<?=$_POST["meta_keywords"]?>" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label" for="page_description">Мета описание</label>
                    <textarea id="page_description" name="meta_description" class="form-control"><?=$_POST["meta_description"]?></textarea>
                </div>
                <div class="form-group">
                    <textarea id="content" name="content" style="width:100%; height:320px;"><?=$_POST["content"]?></textarea>
                </div>
            </div>
            <div class="tab-pane" id="settings">
                <div class="form-group<? if (isset($error["rewrite"])) echo " has-error"; ?>">
                    <label class="control-label" for="page_rewrite">Ссылка (ЧПУ)</label>
                    <input type="text" id="page_rewrite" name="rewrite" value="<?=$_POST["rewrite"]?>" class="form-control"<? if ($_POST["protected"]==1) echo " disabled"; ?>>
                    <? if (isset($error["rewrite"])) { ?><span class="help-block"><?=$error["rewrite"]?></span><? } ?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="pageid">Родитель</label>
                    <select id="pageid" name="pid" class="form-control">
                        <option value="0">-нет-</option>
                        <?
                        $query=@mysqli_query($mysql, "select * from `pages` where `pid`='0' order by `order` asc");
                        while ($row=@mysqli_fetch_assoc($query)) {
                            ?>
                            <option value="<?=@$row["id"]?>"<? if (@$row["id"]==@$_POST["pid"]) echo " selected"; ?>><?=@$row["title"]?></option>
                            <?
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label" for="template">Шаблон</label>
                    <select id="template" name="template" class="form-control">
                        <option value="blank"<? if ($_POST["template"]=='blank') echo " selected"; ?>>Текстовая страница</option>
                        <option value="news"<? if ($_POST["template"]=='news') echo " selected"; ?>>Новости</option>
                    </select>
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
                        <option value="1"<? if ($_POST["visible"]=='1') echo " selected"; ?>>Видна в меню</option>
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
            <h2>Страницы</h2>
        </div>
        <div class="text-right row-phone">
            <a href="./?c=<?=$c?>&amp;action=edit_page" title="Создать новую страницу"
               class="btn btn-phone btn-primary">Создать новую страницу</a>
            <a href="./?c=<?=$c?>&amp;action=edit_page&amp;id=1" title="Редактировать страницу"
               class="btn btn-phone btn-default">Редактировать страницу 404</a>
        </div>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th></th>
                <th>Название</th>
                <th>ЧПУ</th>
                <th>Статус</th>
                <th>Доступ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?
            $query=@mysqli_query($mysql, "select * from `pages` where `pid`='0' and `id`>'1' order by `order` asc");
            while ($row=@mysqli_fetch_assoc($query)) {
                $query1=@mysqli_query($mysql, "select * from `pages` where `pid`='" . @$row["id"] . "' order by `order` asc");
                $haschild=FALSE;
                if (@mysqli_num_rows($query1)) $haschild=TRUE;

                drawpagerow($row, $haschild);

                if ($haschild) {
                    while ($row1=@mysqli_fetch_assoc($query1)) {
                        drawpagerow($row1, FALSE, TRUE);
                    }
                }
            }
            ?>
        </tbody>
    </table>

    <script type="text/javascript">
        $(document).ready(function(){
            $("a.parent").click(function(e){
                e.preventDefault();
                id=$(this).data('id');
                $("tr").each(function(index,element){
                    if($(element).data('pid')==id)$(element).toggle(0);
                });
                if($(this).hasClass('btn-collapse')) {
                    $(this).removeClass('btn-collapse').addClass('btn-expand').find("span").removeClass('glyphicon-minus').addClass('glyphicon-plus');
                    Cookies.set('collapse['+id+']', '1',{expires:999});
                }else {
                    $(this).removeClass('btn-expand').addClass('btn-collapse').find("span").removeClass('glyphicon-plus').addClass('glyphicon-minus');
                    Cookies.remove('collapse['+id+']');
                }
            });
        });
    </script>
    <?

}


function drawpagerow ($row, $haschild=FALSE, $child=FALSE) {
    global $c;

    ?>
    <tr<? if ($child) { ?> data-pid="<?=@$row["pid"]?>" rel="child"<? } ?> style="<? if(@$_COOKIE["collapse"][@$row["pid"]]==1){echo "display:none;";}else{echo "";}?>">
        <td><? if ($haschild && !$child) { ?>
                <a href="" class="<? if(@$_COOKIE["collapse"][@$row["id"]]==1){echo "btn-expand";}else{echo "btn-collapse";}?> parent" data-id="<?=@$row["id"]?>"><span class="glyphicon <? if(@$_COOKIE["collapse"][@$row["id"]]==1){echo "glyphicon-plus";}else{echo "glyphicon-minus";}?>"></span></a>
            <? } ?></td>
        <td>
            <? if ($child) { ?><span class="spacer"></span> <? } ?>
            <a href="./?c=<?=$c?>&amp;action=move-up&amp;id=<?=@$row["id"]?>" title="Выше"><span class="glyphicon glyphicon-arrow-up"></span></a>
            <a href="./?c=<?=$c?>&amp;action=move-down&amp;id=<?=@$row["id"]?>" title="Ниже"><span class="glyphicon glyphicon-arrow-down"></span></a>
            <?=@$row["title"]?>
        </td>
        <td>/<?=@$row["rewrite"]!='/'?@$row["rewrite"]:''?><? if(@$row["protected"]==1)echo ' <span class="glyphicon glyphicon-lock text-warning small"></span>';?></td>
        <td><? if (@$row["published"]==1) {
                echo "Опубликована";
            } else {
                echo "Черновик";
            } ?></td>
        <td><? if (@$row["visible"]==1) {
                echo "Видна в меню";
            } else {
                echo "Скрыта";
            } ?></td>
        <td nowrap>
            <div class="pull-right">
                <div class="btn-group">
                    <a href="./?c=<?=$c?>&amp;action=edit_page&amp;id=<?=@$row["id"]?>" class="btn btn-primary">Редактировать</a>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="./?c=<?=$c?>&amp;action=edit_page&amp;pid=<?=@$row["id"]?>" title="Добавить">Добавить</a>
                        </li>
                        <li>
                            <a href="./?c=<?=$c?>&amp;action=clone_page&amp;id=<?=@$row["id"]?>" title="Скопировать">Скопировать</a>
                        </li>
                        <li class="divider"></li>
                        <li class="dropdown-header">Статус</li>
                        <li>
                            <a href="./?c=<?=$c?>&amp;action=published&amp;id=<?=@$row["id"]?>">
                                Опубликована
                                <? if (@$row["published"]==1) { ?>
                                    <span class="glyphicon glyphicon-ok"></span>
                                <? } ?>
                            </a>
                        </li>
                        <li>
                            <a href="./?c=<?=$c?>&amp;action=draft&amp;id=<?=@$row["id"]?>">
                                Черновик
                                <? if (@$row["published"]==0) { ?>
                                    <span class="glyphicon glyphicon-ok"></span>
                                <? } ?>
                            </a>
                        </li>
                        <li class="dropdown-header">Доступ</li>
                        <li>
                            <a href="./?c=<?=$c?>&amp;action=show&amp;id=<?=@$row["id"]?>">
                                Видна в меню
                                <? if (@$row["visible"]==1) { ?>
                                    <span class="glyphicon glyphicon-ok"></span>
                                <? } ?>
                            </a>
                        </li>
                        <li>
                            <a href="./?c=<?=$c?>&amp;action=hide&amp;id=<?=@$row["id"]?>">
                                Скрыта
                                <? if (@$row["visible"]==0) { ?>
                                    <span class="glyphicon glyphicon-ok"></span>
                                <? } ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="./?c=<?=$c?>&amp;action=delete_page&amp;id=<?=@$row["id"]?>" class="btn btn-danger"<? if(@$row["protected"]==1)echo " disabled";?> onclick="return confirm('Удалить страницу?')">Удалить</a>
            </div>
        </td>
    </tr>

    <?
}


function repairpagesorder ($pid) {
    global $mysql;

    $i=0;
    $query=@mysqli_query($mysql, "select * from `pages` where `pid`='" . $pid . "' order by `order` asc");
    while ($row=@mysqli_fetch_assoc($query)) {
        @mysqli_query($mysql, "update `pages` set `order`='" . $i . "' where `id`='" . @$row["id"] . "'");
        $i++;
    }
}