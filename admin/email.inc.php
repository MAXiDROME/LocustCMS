<?
//редактор email_template.html


if (isset($_POST["save_email"])) {
    file_put_contents('../templates/email_template.html',($_POST["content"]));
    unset($_POST);
    admin_log('Изменение шаблона электронных писем');
}

if (!isset($_POST["save_email"])) {
    $_POST["content"]=file_get_contents('../templates/email_template.html');
}

array_walk($_POST, 'makesafeformstring');

?>
<div class="vertical-align margin-bottom-1">
    <div class="text-left row-phone">
        <h2>Редактирование дизайн-шаблона электронных писем</h2>
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
    <div class="row">
        <div class="col-xs-12">
            <code>###SUBJECT###</code> - место, куда будет вставляться тема письма<br>
            <code>###MESSAGE###</code> - место, куда будет вставляться текст письма
        </div>
    </div>
    <div class="row margin-top-1">
        <div class="col-sm-6">
            <input type="submit" id="save_email" name="save_email" value="Сохранить" class="btn btn-phone btn-primary">&nbsp;
            <a href="./?c=<?=$c?>" title="Отмена" class="btn btn-phone btn-default">Отмена</a>
        </div>
    </div>
</form>
