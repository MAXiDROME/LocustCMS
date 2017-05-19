<h1>Рабочий стол</h1>

<div class="row">
    <?
    foreach ($_cms_pages as $_name=>$_cat){
        unset($_indexes);
        foreach ($_cat as $_index){
            $_indexes[]=$_index;
        }
        if(check_user_access($_indexes)) {
            ?>
            <div class="col-xs-12 col-sm-4">
                <h3><?=$_name?></h3>
                <ul class="list-unstyled">
                    <?
                    foreach ($_cat as $_page_name=>$_page_index) {
                        if (@in_array($_page_index, $_SESSION["admin_auth_user"]["role"])!==FALSE) {
                            ?>
                            <li><a href="./?c=<?=$_page_index?>"><?=$_page_name?></a></li>
                            <?
                        }
                    }
                    ?>
                </ul>
            </div>
            <?
        }
    }
    ?>
</div>

