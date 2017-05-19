<?
//редактор icons.svg

//phpinfo();exit;

if (isset($_POST["save_svg"])) {
    file_put_contents('../img/icons.svg',($_POST["content"]));
    unset($_POST);
    admin_log('Изменение файла с SVG-иконками');
}

if (!isset($_POST["save_svg"])) {
    $_POST["content"]=@file_get_contents('../img/icons.svg');
}

array_walk($_POST, 'makesafeformstring');

?>
<div class="vertical-align margin-bottom-1">
    <div class="text-left row-phone">
        <h2>Редактирование иконок</h2>
    </div>
</div>

<form action="." method="post">
    <input type="hidden" name="c" value="<?=$c?>">
    <div class="row margin-bottom-1">
        <div class="col-xs-12">
            <div>
                <textarea id="content" name="content" style="width:100%; height:500px;"><?=$_POST["content"]?></textarea>
            </div>
        </div>
    </div>
    <div class="row margin-top-1">
        <div class="col-sm-6">
            <input type="submit" id="save_svg" name="save_svg" value="Сохранить" class="btn btn-phone btn-primary">&nbsp;
            <a href="./?c=<?=$c?>" title="Отмена" class="btn btn-phone btn-default">Отмена</a>
        </div>
    </div>
</form>
