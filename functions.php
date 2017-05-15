<?php
//разные функции (ну не люблю я ООП =))


function parent_tree ($p) {//делаем дерево родителей для выбранной страницы
    global $pagesarr, $mysql;
    $query=@mysqli_query($mysql, "select `pid` from `pages` where `id`='$p'");
    $row=@mysqli_fetch_assoc($query);
    if (@$row["pid"]>0) parent_tree($row["pid"]);
    $pagesarr[]=@$p;
}


/**
 * @param        $allpage   int число страниц
 * @param        $this_page int текущая страница
 * @param string $query     string url-строка с тегом {p} в качестве номера страницы
 * @param int    $flp       int показывать первую и последнюю страницу (1=да)
 * @param int    $expanded  int расширенная навигация (больше страниц, 1=да)
 */
function draw_navigation ($allpage, $this_page, $query="", $flp=1, $expanded=1) {
    // {p} в $query заменяется на номер страницы, пример index.php?p={p}
    // $flp - вывод первой и последней страницы
    // $expanded - расширенная навигация
    //    $this_page = (isset($_GET['p'])) ? intval($_GET['p']) : 1 ;
    if ($this_page<1) $this_page=1;
    if ($this_page>$allpage) $this_page=@$allpage;

    $prev_page=$this_page-1;
    $pprev_page=$this_page-2;
    $ppprev_page=$this_page-3;
    $next_page=$this_page+1;
    $nnext_page=$this_page+2;
    $nnnext_page=$this_page+3;

    ?>
    <!-- Bootstrap Pagination-->
    <nav>
        <ul class="pagination pagination">
            <?
            // Первая страница
            if ($pprev_page<=2 || @$allpage==5) {
                if ($this_page>2 && $flp==1) {
                    ?>
                    <li><a href="<?=str_replace('{p}', 1, $query)?>">1</a></li>
                    <?
                }
            } else {
                if ($this_page>2 && $flp==1) {
                    ?>
                    <li><a href="<?=str_replace('{p}', 1, $query)?>">1</a></li>
                    <li><span style="padding-left:0;padding-right:0;background:transparent;border:none;">...</span></li>
                    <?
                }
            }

            // Пред пред предыдущая страница
            if ($ppprev_page>1 && $expanded==1 && $allpage==5) {
                ?>
                <li><a href="<?=str_replace('{p}', $ppprev_page, $query)?>"><?=$ppprev_page?></a></li>
                <?
            }
            // Пред предыдущая страница
            if ($pprev_page>1 && $expanded==1) {
                ?>
                <li><a href="<?=str_replace('{p}', $pprev_page, $query)?>"><?=$pprev_page?></a></li>
                <?
            }
            // Предыдущая страница
            if ($prev_page>=1) {
                ?>
                <li><a href="<?=str_replace('{p}', $prev_page, $query)?>"><?=$prev_page?></a></li>
                <?
            }

            // Наша позиция
            ?>
            <li class="active"><span><?=$this_page?></span></li>
            <?
            // Следующая страница
            if ($next_page<=$allpage) {
                ?>
                <li><a href="<?=str_replace('{p}', $next_page, $query)?>"><?=$next_page?></a></li>
                <?
            }
            // Следующая за следующей страница
            if ($nnext_page<$allpage && $expanded==1) {
                ?>
                <li><a href="<?=str_replace('{p}', $nnext_page, $query)?>"><?=$nnext_page?></a></li>
                <?
            }
            // Следующая за следующей следующей страница
            if ($nnnext_page<$allpage && $expanded==1 && $allpage==5) {
                ?>
                <li><a href="<?=str_replace('{p}', $nnnext_page, $query)?>"><?=$nnnext_page?></a></li>
                <?
            }
            //Последняя страница
            if ($nnext_page>=$allpage-1 || @$allpage==5) {
                if ($this_page<$allpage-1 && $flp==1) {
                    ?>
                    <li><a href="<?=str_replace('{p}', $allpage, $query)?>"><?=$allpage?></a></li>
                    <?
                }
            } else {
                if ($this_page<$allpage-1 && $flp==1) {
                    ?>
                    <li><span style="padding-left:0;padding-right:0;background:transparent;border:none;">...</span></li>
                    <li><a href="<?=str_replace('{p}', $allpage, $query)?>"><?=$allpage?></a></li>
                    <?
                }
            }

            ?>
        </ul>
    </nav>


    <?
}


function makesafesqlstring (&$str) {
    global $mysql;

    if (is_array($str)) {
        array_walk($str, 'makesafesqlstring');
    } else {
        $str=@mysqli_real_escape_string($mysql, $str);
    }

}

function makesafeformstring (&$str) {

    if (is_array($str)) {
        array_walk($str, 'makesafeformstring');
    } else {
        $str=htmlspecialchars(stripcslashes($str));
    }

}

function makevalidurl ($to_url, $keepspaces=0) {
    $trans=array('А'=>'A', 'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ё'=>'Jo', 'Ж'=>'Zh', 'З'=>'Z', 'И'=>'I', 'Й'=>'J', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R', 'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Х'=>'H', 'Ц'=>'C', 'Ч'=>'Ch', 'Ш'=>'Sh', 'Щ'=>'Shh', 'Ъ'=>'', 'Ы'=>'Y', 'Ь'=>'', 'Э'=>'Je', 'Ю'=>'Ju', 'Я'=>'Ja',

                 'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ё'=>'jo', 'ж'=>'zh', 'з'=>'z', 'и'=>'i', 'й'=>'j', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p', 'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'х'=>'h', 'ц'=>'c', 'ч'=>'ch', 'ш'=>'sh', 'щ'=>'shh', 'ъ'=>'', 'ы'=>'y', 'ь'=>'', 'э'=>'je', 'ю'=>'ju', 'я'=>'ja',
    );

    $url=strtr($to_url, $trans);    // Заменяем кириллицу согласно массиву замены
    $url=mb_strtolower($url);          // В нижний регистр

    $url=preg_replace("/[^a-z0-9\s,\.-]/i", "", $url);  // Удаляем лишние символы
    $url=preg_replace("/[,-]/ui", " ", $url);         // Заменяем на пробелы

    if ($keepspaces==0) {
        $url=preg_replace("/[\s]+/ui", "-", $url);         // Заменяем 1 и более пробелов на "-"
    }

    return $url;
}


function regexpEscape ($str) {
    return preg_quote($str, '/');
}


function sendmail ($email, $subj, $msg, $from='') {
    global $_config;

    if ($from=='') $from='=?utf-8?B?' . base64_encode($_config["firm_name"]) . '?=' . " <" . @$_config["email"] . ">";

    $subj='=?utf-8?B?' . base64_encode($subj) . '?=';
    $headers='MIME-Version: 1.0' . "\r\n";
    $headers.="From: " . @$from . "\r\n";
    $headers.='Content-type: text/html; charset=utf-8' . "\r\n";

    return @mail($email, $subj, $msg, $headers);
}

function sendmailbeauty ($email, $subj, $msg, $delayed=0, $from='') {
    global $mysql;

    //готовим шаблоны
    //вставляем $msg в шаблон
    $tpl=file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/templates/email_template.html');
    $tpl=@str_replace('###SUBJECT###', $subj, $tpl);
    $tpl=@str_replace('###MESSAGE###', $msg, $tpl);

    if ($delayed) {
        @mysqli_query($mysql, "INSERT INTO `emails` (`email`,`subject`,`message`) VALUES ('" . @mysqli_real_escape_string($mysql, $email) . "','" . @mysqli_real_escape_string($mysql, $subj) . "','" . @mysqli_real_escape_string($mysql, $tpl) . "')");
    } else {
        sendmail($email, $subj, $tpl, $from);
    }
}

// $Counter – Количество того что мы считаем
// $txtBase – База слова (может быть пустой)
// $txt1 – Окончание или слово для того что мы считаем, в количестве одной штуки.
// $txt2 - Окончание или слово для того что мы считаем, в количестве двух штук.
// $txt5 - Окончание или слово для того что мы считаем, в количестве пяти штук.
// $ShowNum – Возвращать количество или нет (true или false)
//Считаем возраст пользователей: BuildCountText($i, "", "Год", "Года", "Лет", true)
//Считаем «пацанов»: BuildCountText($i, "Пацан", "", "а", "ов", true)
//Считаем попугаев: BuildCountText($i, "Попуга", "й", "я", "ев", true)
//Считаем рубли : BuildCountText($i, "Рубл", "ь", "я", "ей", true)
//Считаем копейки: BuildCountText($i, "Копе", "йка", "йки", "ек", true)
//Считаем баксы: BuildCountText($i, "Бакс", "", "а", "ов", true)
//Считаем поездки: BuildCountText($i, "Поезд", "ка", "ки", "ок", true)
//Считаем мобильники: BuildCountText($i, "Мобильни", "к", "ка", "ков", true)
//Считаем подарки: BuildCountText($i, "Подар", "ок", "ка", "ков", true)
//Считаем «штучки»: BuildCountText($i, "Штуч", "ка", "ки", "ек", true)
function BuildCountText ($Counter, $txtBase="", $txt1="", $txt2="", $txt5="", $ShowNum=TRUE) {
    if (($Counter<=14) && ($Counter>=5)) {
        $str=$txt5;
    } else {
        $num=$Counter-(floor($Counter/10)*10);
        if ($num==1) {
            $str=$txt1;
        } elseif ($num==0) {
            $str=$txt5;
        } elseif (($num>=2) && ($num<=4)) {
            $str=$txt2;
        } elseif (($num>=5) && ($num<=9)) {
            $str=$txt5;
        }
    }
    if ($ShowNum) {
        return $Counter . " " . $txtBase . $str;
    } else {
        return $txtBase . $str;
    }
}


function semantic ($i, &$words, &$fem, $f) {

    $_1_2[1]="одна ";
    $_1_2[2]="две ";

    $_1_19[1]="один ";
    $_1_19[2]="два ";
    $_1_19[3]="три ";
    $_1_19[4]="четыре ";
    $_1_19[5]="пять ";
    $_1_19[6]="шесть ";
    $_1_19[7]="семь ";
    $_1_19[8]="восемь ";
    $_1_19[9]="девять ";
    $_1_19[10]="десять ";

    $_1_19[11]="одиннацать ";
    $_1_19[12]="двенадцать ";
    $_1_19[13]="тринадцать ";
    $_1_19[14]="четырнадцать ";
    $_1_19[15]="пятнадцать ";
    $_1_19[16]="шестнадцать ";
    $_1_19[17]="семнадцать ";
    $_1_19[18]="восемнадцать ";
    $_1_19[19]="девятнадцать ";

    $des[2]="двадцать ";
    $des[3]="тридцать ";
    $des[4]="сорок ";
    $des[5]="пятьдесят ";
    $des[6]="шестьдесят ";
    $des[7]="семьдесят ";
    $des[8]="восемдесят ";
    $des[9]="девяносто ";

    $hang[1]="сто ";
    $hang[2]="двести ";
    $hang[3]="триста ";
    $hang[4]="четыреста ";
    $hang[5]="пятьсот ";
    $hang[6]="шестьсот ";
    $hang[7]="семьсот ";
    $hang[8]="восемьсот ";
    $hang[9]="девятьсот ";


    $words="";
    $fl=0;
    if ($i>=100) {
        $jkl=intval($i/100);
        $words.=$hang[$jkl];
        $i%=100;
    }
    if ($i>=20) {
        $jkl=intval($i/10);
        $words.=$des[$jkl];
        $i%=10;
        $fl=1;
    }
    switch ($i) {
        case 1:
            $fem=1;
            break;
        case 2:
        case 3:
        case 4:
            $fem=2;
            break;
        default:
            $fem=3;
            break;
    }
    if ($i) {
        if ($i<3 && $f>0) {
            if ($f>=2) {
                $words.=$_1_19[$i];
            } else {
                $words.=$_1_2[$i];
            }
        } else {
            $words.=$_1_19[$i];
        }
    }
}


function num2str ($L) {
    $namerub[1]="рубль ";
    $namerub[2]="рубля ";
    $namerub[3]="рублей ";

    $nametho[1]="тысяча ";
    $nametho[2]="тысячи ";
    $nametho[3]="тысяч ";

    $namemil[1]="миллион ";
    $namemil[2]="миллиона ";
    $namemil[3]="миллионов ";

    $namemrd[1]="миллиард ";
    $namemrd[2]="миллиарда ";
    $namemrd[3]="миллиардов ";

    $kopeek[1]="копейка ";
    $kopeek[2]="копейки ";
    $kopeek[3]="копеек ";

    $s=" ";
    $s1=" ";
    $s2=" ";
    $kop=intval(($L*100-intval($L)*100));
    $L=intval($L);
    if ($L>=1000000000) {
        $many=0;
        semantic(intval($L/1000000000), $s1, $many, 3);
        $s.=$s1 . $namemrd[$many];
        $L%=1000000000;
    }

    if ($L>=1000000) {
        $many=0;
        semantic(intval($L/1000000), $s1, $many, 2);
        $s.=$s1 . $namemil[$many];
        $L%=1000000;
    }

    if ($L>=1000) {
        $many=0;
        semantic(intval($L/1000), $s1, $many, 1);
        $s.=$s1 . $nametho[$many];
        $L%=1000;
    }

    if ($L!=0) {
        $many=0;
        semantic($L, $s1, $many, 0);
        $s.=$s1;
    }

    if ($kop>0) {
        $many=0;
        semantic($kop, $s1, $many, 1);
        $s.=$s1;
    } else {
    }

    return $s;
}


function password_generator ($len) {
    $alphabet='abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ1234567890';
    $pass=array();
    $alphaLength=strlen($alphabet)-1;
    for ($i=0; $i<$len; $i++) {
        $n=mt_rand(0, $alphaLength);
        $pass[]=$alphabet[$n];
    }

    return implode($pass);
}



function checkemail ($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return TRUE;
    }

    return FALSE;
}

function login_user ($id) {
    global $mysql, $login_error;
    $query=@mysqli_query($mysql, "SELECT * FROM `users` WHERE `id`='" . $id . "'");
    if ($row=@mysqli_fetch_assoc($query)) {
        unset($row["password"], $row["blocked"]);
        $_SESSION["auth_user"]=$row;
        @mysqli_query($mysql,"update `users` set `lastlogin`=now() where `id`='".$id."'");
    } else {
        $login_error="User not found";
    }

}

function short_description ($str) {
    if (mb_strlen($str)<=140) return $str;

    $tmp=mb_substr($str, 0, 125);
    $tmp1=mb_substr($str, 125);
    $tmp2=explode(" ", $tmp1);
    $tmp.=$tmp2[0] . "&hellip;";

    return $tmp;
}

function sendpulse_addemail ($email, $addressbook) {

    if (1) {

        $sendpulse_api_token="https://api.sendpulse.com/oauth/access_token";
        $sendpulse_api_addemail="https://api.sendpulse.com/addressbooks/" . $addressbook . "/emails";

        $json_arr[0]["email"]=$email;
        $json_arr[0]["variables"]=[];

        $sendpulse_api_token_params=[
            "grant_type"   =>"client_credentials",
            "client_id"    =>sendpulse_id,
            "client_secret"=>sendpulse_secret,
        ];

        $myCurl=curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL           =>$sendpulse_api_token,
            CURLOPT_RETURNTRANSFER=>TRUE,
            CURLOPT_POST          =>TRUE,
            CURLOPT_POSTFIELDS    =>http_build_query($sendpulse_api_token_params),
        ));
        $response=json_decode(curl_exec($myCurl), TRUE);
        curl_close($myCurl);

        $sendpulse_auth="Authorization: " . $response["token_type"] . " " . $response["access_token"] . "";

        $myCurl=curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_HTTPHEADER    =>array($sendpulse_auth),
            CURLOPT_URL           =>$sendpulse_api_addemail,
            CURLOPT_RETURNTRANSFER=>TRUE,
            CURLOPT_POST          =>TRUE,
            CURLOPT_POSTFIELDS    =>http_build_query(array("emails"=>serialize($json_arr))),
        ));

        //$response = json_decode(curl_exec($myCurl),TRUE);
        $response=curl_exec($myCurl);
        curl_close($myCurl);

        //echo $response;

    }

}


/**
 * @param        $src  string источник картинки
 * @param        $dst  string результат
 * @param int    $w    int ширина
 * @param int    $h    int высота
 * @param int    $q    int качество
 * @param int    $crop int выбор режима (
 *                     1-ресайз, потом кроп что выступает,
 *                     2- ресайзим и заливаем фоном что выступает
 *                     )
 * @param string $bg   string цвет заливки
 */
function makethumb ($src, $dst, $w=400, $h=300, $q=100, $crop=1, $bg='#FFFFFF') {
    // Get new sizes
    list($width, $height, $type)=getimagesize($src);

    //загружаем исходный файл
    switch ($type) {
        case IMAGETYPE_JPEG:
            $image=imagecreatefromjpeg($src);
            break;
        case IMAGETYPE_GIF:
            $image=imagecreatefromgif($src);
            break;
        case IMAGETYPE_PNG:
            $image=imagecreatefrompng($src);
            break;
        default:
            break;
    }
    //    @imagejpeg($image, $dst, $q);


    $srcx=0;
    $srcy=0;
    $startx=0;
    $starty=0;

    $image_p=imagecreatetruecolor(@$w, @$h);


    if (@$crop==1) {//кропаем файл
        $new_width=$w;
        $new_height=$h;
        if ($w/$width>$h/$height) {//картинка широкая - кропаем вертикаль
            $height1=$h*$width/$w;
            $srcy=($height-$height1)/2;
            $height=$height1;
            $srcx=0;
        } else {//картинка высокая - кропаем горизонталь
            $width1=$w*$height/$h;
            $srcx=($width-$width1)/2;
            $width=$width1;
            $srcy=0;
        }
    } elseif (@$crop==2) {//не кропаем, а заливаем границы фоном
        if ($width>$height) {
            $new_width=@$w;
            $new_height=$height*@$w/$width;
            $startx=0;
            $starty=(@$h-$new_height)/2;
        } else {
            $new_height=@$h;
            $new_width=$width*@$h/$height;
            $startx=(@$w-$new_width)/2;
            $starty=0;

        }

        $col_red="0x" . substr($bg, 1, 2)+0;
        $col_green="0x" . substr($bg, 3, 2)+0;
        $col_blue="0x" . substr($bg, 5, 2)+0;
        imagefill($image_p, 0, 0, imagecolorallocate($image_p, $col_red, $col_green, $col_blue));

    }

    imagecopyresampled($image_p, $image, $startx, $starty, $srcx, $srcy, $new_width, $new_height, $width, $height);

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($image_p, $dst, $q);
            break;
        case IMAGETYPE_GIF:
            imagegif($image_p, $dst);
            break;
        case IMAGETYPE_PNG:
            imagepng($image_p, $dst);
            break;
        default:
            break;
    }

    @chmod($dst, 0666);
    @imagedestroy($image_p);
    @imagedestroy($image);

}
//конец функции makethumb


function getcurrencyrates(){
    global $THBUSD,$THBRUB,$THBEUR,$THBCNY;
    $currencyurl="https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%3D%22thbusd%2Cthbrub%2Cthbcny%2Cthbeur%22&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=";
    $currentcurrency=@json_decode(file_get_contents($currencyurl),true);
    $currencyrates=@$currentcurrency["query"]["results"]["rate"];
    for($i=0;$i<count(@$currencyrates);$i++){
        ${@$currencyrates[$i]["id"]}=@$currencyrates[$i]["Rate"];
    }
}

function parse_blocks(&$text){
    global $mysql;

    $query=@mysqli_query($mysql,"select * from `blocks` where 1");
    while($row=@mysqli_fetch_assoc($query)){
        $text=str_replace("###".@$row["name"]."###",@$row["content"],$text);
    }

}

function pagenotfound(){
    global $mysql,$_page,$_config,$mPageUrl,$meta_description,$meta_keywords,$meta_title;
    header("HTTP/1.0 404 Not Found");
    $query=@mysqli_query($mysql, "select *, substring('$mPageUrl' from length(`rewrite`)+1) `trail` from `pages` where `id`='1'");
    $_page=@mysqli_fetch_assoc($query);

    $meta_title=$_page["meta_title"]!='' ? $_page["meta_title"] : $_page["title"];
    $meta_description=$_page["meta_description"]!='' ? $_page["meta_description"] : $_config["meta_description"];
    $meta_keywords=$_page["meta_keywords"]!='' ? $_page["meta_keywords"] : $_config["meta_keywords"];
}
