<?

if(isset($_POST["save_settings"])){

    array_walk($_POST, 'makesafesqlstring');

    if($_POST["login"]=="")$error["login"]="Обязательное поле";
    if($_POST["password"]=="")$error["password"]="Обязательное поле";
    if($_POST["firm_name"]=="")$error["firm_name"]="Обязательное поле";
    if($_POST["email"]=="")$error["email"]="Обязательное поле";
    if($_POST["phone"]=="")$error["phone"]="Обязательное поле";
    if($_POST["pagesize"]=="")$error["pagesize"]="Обязательное поле";
    $_POST["maintenance"]=@$_POST["maintenance"]*1;

    if(!isset($error)){

        @mysqli_query($mysql, "update `config` set 
                                                    `firm_name`='".@$_POST["firm_name"]."',
                                                    `email`='".@$_POST["email"]."',
                                                    `phone`='".@$_POST["phone"]."',
                                                    `login`='".@$_POST["login"]."',
                                                    `password`='".@$_POST["password"]."',
                                                    `pagesize`='".@$_POST["pagesize"]."',
                                                    `counters`='".@$_POST["counters"]."',
                                                    `meta_title`='".@$_POST["meta_title"]."',
                                                    `meta_keywords`='".@$_POST["meta_keywords"]."',
                                                    `meta_description`='".@$_POST["meta_description"]."'
                                              where `id`='".$_config["id"]."'");


        $query=@mysqli_query($mysql, "select * from `config` where `id`='".$_config["id"]."'");
        $_config=@mysqli_fetch_assoc($query);

        unset($_POST);
        unset($_GET);
    }
}


if(!isset($_POST["save_settings"])){
    $_POST=$_config;
}

array_walk($_POST, 'makesafeformstring');

?>

<div class="vertical-align margin-bottom-1">
    <div class="text-left row-phone">
        <h2>Настройки</h2>
    </div>
</div>

<form action="." method="post">
    <input type="hidden" name="c" value="<?=$c?>">

    <div class="margin-bottom-1">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="maintenance" value="1"<? if($_POST["maintenance"]==1)echo " checked";?>> закрыть на обслуживание
            </label>
        </div>
        <div class="form-group<? if (isset($error["login"])) echo " has-error"; ?>">
            <label class="control-label" for="settings_login">Логин</label>
            <input type="text" id="settings_login" name="login" value="<?=$_POST["login"]?>" class="form-control">
            <? if (isset($error["login"])) { ?><span class="help-block"><?=$error["login"]?></span><? } ?>
        </div>
        <div class="form-group<? if (isset($error["password"])) echo " has-error"; ?>">
            <label class="control-label" for="settings_password">Пароль</label>
            <input type="text" id="settings_password" name="password" value="<?=$_POST["password"]?>" class="form-control">
            <? if (isset($error["password"])) { ?><span class="help-block"><?=$error["password"]?></span><? } ?>
        </div>
        <div class="form-group<? if (isset($error["firm_name"])) echo " has-error"; ?>">
            <label class="control-label" for="settings_firmname">Название компании</label>
            <input type="text" id="settings_firmname" name="firm_name" value="<?=$_POST["firm_name"]?>" class="form-control">
            <? if (isset($error["firm_name"])) { ?><span class="help-block"><?=$error["firm_name"]?></span><? } ?>
        </div>
        <div class="form-group<? if (isset($error["email"])) echo " has-error"; ?>">
            <label class="control-label" for="settings_email">Адрес электронной почты</label>
            <input type="text" id="settings_email" name="email" value="<?=$_POST["email"]?>" class="form-control">
            <? if (isset($error["email"])) { ?><span class="help-block"><?=$error["email"]?></span><? } ?>
        </div>
        <div class="form-group<? if (isset($error["phone"])) echo " has-error"; ?>">
            <label class="control-label" for="settings_phone">Телефон</label>
            <input type="text" id="settings_phone" name="phone" value="<?=$_POST["phone"]?>" class="form-control">
            <? if (isset($error["phone"])) { ?><span class="help-block"><?=$error["phone"]?></span><? } ?>
        </div>
        <div class="form-group">
            <label class="control-label" for="settings_meta_title">Мета заголовок</label>
            <input type="text" id="settings_meta_title" name="meta_title" value="<?=$_POST["meta_title"]?>" class="form-control">
        </div>
        <div class="form-group">
            <label class="control-label" for="settings_keywords">Мета ключевые слова</label>
            <input type="text" id="settings_keywords" name="meta_keywords" value="<?=$_POST["meta_keywords"]?>" class="form-control">
        </div>
        <div class="form-group">
            <label class="control-label" for="settings_description">Мета описание</label>
            <textarea id="settings_description" name="meta_description" class="form-control"><?=$_POST["meta_description"]?></textarea>
        </div>
        <div class="form-group<? if (isset($error["pagesize"])) echo " has-error"; ?>">
            <label class="control-label" for="settings_pagesize">Элементов на странице</label>
            <input type="text" id="settings_pagesize" name="pagesize" value="<?=$_POST["pagesize"]?>" class="form-control">
            <? if (isset($error["pagesize"])) { ?><span class="help-block"><?=$error["pagesize"]?></span><? } ?>
        </div>
        <div class="form-group">
            <label class="control-label" for="settings_counters">Коды счетчиков</label>
            <textarea id="settings_counters" name="counters" class="form-control"><?=$_POST["counters"]?></textarea>
        </div>
    </div>

    <div class="row margin-top-1">
        <div class="col-sm-6">
            <input type="submit" id="save_settings" name="save_settings" value="Сохранить" class="btn btn-phone btn-primary">&nbsp;
            <a href="./?c=<?=$c?>" title="Отмена" class="btn btn-phone btn-default">Отмена</a>
        </div>
    </div>
</form>

