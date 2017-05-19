<?

include_once '../config.php';
include_once 'config.inc.php';
include_once 'functions.php';

$c="";
if (isset($_POST["c"])) $_GET["c"]=$_POST["c"];
if (isset($_GET["c"])) $c=$_GET["c"];

if (isset($_POST["login"]) && isset($_POST["password"]) && !isset($_SESSION["admin_auth_user"])) {
    $error="";
    $login=mysqli_real_escape_string($mysql, $_POST["login"]);
    $password=mysqli_real_escape_string($mysql, $_POST["password"]);
    if ($login=="") $error.="Не указан логин!<br>";
    if ($password=="") $error.="Не указан пароль!<br>";

    $query=@mysqli_query($mysql,"select * from `admin_users` where `login`='".$login."'");
    if(!$row=@mysqli_fetch_assoc($query)){
        $error.="Неправильный логин!<br>";
    }else{
        if ($password!=$row["password"]) {
            $error.="Неправильный пароль!<br>";
        }
        if ($error=="") {
            $role=@json_decode($row["role"]);
            unset($row["role"]);
            $row["role"]=$role;
            $_SESSION["admin_auth_user"]=$row;
        }
    }
}

if (isset($_GET["logout"])) {
    unset($_SESSION["admin_auth_user"]);
    header("Location: .");
}

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Панель управления сайтом</title>
    <link rel="stylesheet" type="text/css" href="https://yastatic.net/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker3.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="https://yastatic.net/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript" src="https://yastatic.net/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="//cdn.ckeditor.com/4.5.4/standard/ckeditor.js"></script>
    <script type="text/javascript" src="ckfinder/ckfinder.js"></script>
    <script type="text/javascript" src="/js/js.cookie.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/locales/bootstrap-datepicker.ru.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".datepicker").datepicker({
                language: "ru",
                autoclose: true,
                todayBtn: true,
                clearBtn: true,
                todayHighlight: true,
            });
        });
    </script>
</head>
<body>
    <?
    if (!isset($_SESSION["admin_auth_user"])) {
        include_once 'login.inc.php';
    } else {
        ?>
        <nav class="navbar navbar-default navbar-inverse">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
                        <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="."><span class="glyphicon glyphicon-wrench"></span> <?=@$_config["firm_name"]?></a>
                </div>
                <div class="collapse navbar-collapse" id="navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <?
                        foreach ($_cms_pages as $_name=>$_cat){
                            unset($_indexes);
                            foreach ($_cat as $_index){
                                $_indexes[]=$_index;
                            }

                            if(check_user_access($_indexes)) {
                                ?>
                                <li class="dropdown<? if (@in_array($c, $_indexes)!==FALSE) echo " active"; ?>">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <?=$_name?>
                                        <b class="caret"></b> </a>
                                    <ul class="dropdown-menu">
                                        <?
                                        foreach ($_cat as $_page_name=>$_page_index) {
                                            if (@in_array($_page_index, $_SESSION["admin_auth_user"]["role"])!==FALSE) {
                                                ?>
                                                <li class="<? if ($c==$_page_index) echo "active"; ?>">
                                                    <a href="./?c=<?=$_page_index?>"><?=$_page_name?></a>
                                                </li>
                                                <?
                                            }
                                        }
                                        ?>
                                    </ul>
                                </li>
                                <?
                            }
                        }
                        ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <?
                        if($_SESSION["admin_auth_user"]["id"]=='1') {
                            ?>
                            <li class="<? if ($c=="settings") echo "active"; ?>">
                                <a href="./?c=settings"><span class="glyphicon glyphicon-cog"></span> Настройки</a>
                            </li>
                            <?
                        }
                        ?>
                        <li>
                            <a href="/" target="_blank"><span class="glyphicon glyphicon-new-window"></span> Открыть сайт</a>
                        </li>
                        <li>
                            <a href="./?logout"><span class="glyphicon glyphicon-log-out"></span> Выйти</a>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <?
                    $template='dashboard.inc.php';
                    if($c!="") {
                        if (@in_array($c, $_SESSION["admin_auth_user"]["role"])!==FALSE) {
                            $template=$c . ".inc.php";
                        }
                    }
                    include_once $template;
                    ?>
                    <div class="margin-top-1 margin-bottom-1 hidden-md"></div>
                </div>
            </div>
        </div>

        <footer class="container">
            <p class="pull-right">
            <span>
                &copy; 2010 &ndash; <?=@date("Y")?> <a href="http://ltart.ru/" target="_blank">LocustCMS</a>
            </span>
            </p>
        </footer>
        <?
    }
    ?>
</body>
</html>
