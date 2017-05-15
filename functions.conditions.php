<?php

if(!defined("LocustCMS"))die("err403");

if (isset($_SESSION["auth_user"])) array_walk($_SESSION["auth_user"], 'makesafesqlstring');

if(isset($_GET["exit"])){
    unset($_SESSION["auth_user"]);

    if (isset($_COOKIE["login"])) {
        foreach ($_COOKIE["login"] as $key=>$val) {
            setcookie("login[" . $key . "]", '', time()-3600, '/');
        }
    }
}

if (isset($_COOKIE["login"]) && !isset($_SESSION["auth_user"])) {//кука запомнить меня
    array_walk($_COOKIE["login"], 'makesafesqlstring');

    $query=@mysqli_query($mysql, "select * from `users` where md5(`email`)='" . $_COOKIE["login"]["email"] . "' and md5(`password`)='" . $_COOKIE["login"]["password"] . "'");
    if ($row=@mysqli_fetch_assoc($query)) {
        if(@$row["blocked"]==1) {
            $login_error="User is blocked";
        }else {
            login_user($row["id"]);
        }
    }
}//кука запомнить меня


if(isset($_POST["login"],$_POST["email"],$_POST["password"])){
    $query=@mysqli_query($mysql,"select * from `users` where `email`='".@mysqli_real_escape_string($mysql, $_POST["email"])."'");
    if(@mysqli_num_rows($query)){
        $row=@mysqli_fetch_assoc($query);
        if(@$row["blocked"]==1){
            $login_error="User is blocked";
        }else{
            if(@$row["password"]==$_POST["password"]){
                login_user($row["id"]);

                if (@$_POST["login"]["rememberme"]==1) {
                    unset($_POST["login"]["rememberme"]);

                    $expires=strtotime('+14 days');
                    foreach ($_POST["login"] as $key=>$val) {
                        setcookie("login[" . $key . "]", md5($val), $expires, '/');
                    }

                }

            }else{
                $login_error="Wrong password";
            }
        }
    }else{
        $login_error="User with this e-mail is not registered";
    }
}

if(isset($_POST["register"],$_POST["email"])){
    if(checkemail($_POST["email"])) {
        $query=@mysqli_query($mysql, "select * from `users` where `email`='" . @mysqli_real_escape_string($mysql, $_POST["email"]) . "'");
        if (@mysqli_num_rows($query)) {
            $register_error="E-mail is already taken";
        } else {
            $password=password_generator(6);
            @mysqli_query($mysql, "insert into `users` set
                                                                  `email`='" . @mysqli_real_escape_string($mysql, $_POST["email"]) . "',
                                                                  `password`='" . $password . "'
                                                             ");
            login_user(@mysqli_insert_id($mysql));
            sendmailbeauty($_POST["email"],"Welcome","Your password is: ".$password);
            unset($password);
        }
    }else{
        $register_error="E-mail is incorrect";
    }
}
