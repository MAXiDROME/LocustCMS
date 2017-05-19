<?

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

function makevalidurl($to_url,$keepspaces=0){
    $trans = array('А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
                   'Е' => 'E', 'Ё' => 'Jo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
                   'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
                   'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
                   'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch',
                   'Ш' => 'Sh', 'Щ' => 'Shh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
                   'Э' => 'Je', 'Ю' => 'Ju', 'Я' => 'Ja',

                   'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
                   'е' => 'e', 'ё' => 'jo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
                   'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
                   'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
                   'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
                   'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '',
                   'э' => 'je', 'ю' => 'ju', 'я' => 'ja');

    $url = strtr($to_url, $trans);    // Заменяем кириллицу согласно массиву замены
    $url = mb_strtolower($url);	      // В нижний регистр

    $url = preg_replace("/[^a-z0-9\s,\.-]/i", "", $url);  // Удаляем лишние символы
    $url = preg_replace("/[,-]/ui", " ", $url);         // Заменяем на пробелы

    if($keepspaces==0) {
        $url=preg_replace("/[\s]+/ui", "-", $url);         // Заменяем 1 и более пробелов на "-"
    }

    return $url;
}

/**
 * @param        $src string источник картинки
 * @param        $dst string результат
 * @param int    $w int ширина
 * @param int    $h int высота
 * @param int    $q int качество
 * @param int $crop int выбор режима (
 *                  1-ресайз, потом кроп что выступает,
 *                  2- ресайзим и заливаем фоном что выступает
 *                  )
 * @param string $bg string цвет заливки
 */
function makethumb($src,$dst,$w=400,$h=300,$q=100,$crop=1,$bg='#FFFFFF'){
    // Get new sizes
    list($width, $height, $type) = getimagesize($src);

    //загружаем исходный файл
    switch($type){
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($src);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($src);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($src);
            break;
        default:
            break;
    }
//    @imagejpeg($image, $dst, $q);


    $srcx=0;$srcy=0;$startx=0;$starty=0;

    $image_p = imagecreatetruecolor(@$w, @$h);


    if(@$crop==1){//кропаем файл
        $new_width=$w;
        $new_height=$h;
        if($w/$width>$h/$height){//картинка широкая - кропаем вертикаль
            $height1=$h*$width/$w;
            $srcy=($height-$height1)/2;
            $height=$height1;
            $srcx=0;
        }else{//картинка высокая - кропаем горизонталь
            $width1=$w*$height/$h;
            $srcx=($width-$width1)/2;
            $width=$width1;
            $srcy=0;
        }
    }elseif(@$crop==2){//не кропаем, а заливаем границы фоном
        if($width>$height){
            $new_width=@$w;
            $new_height=$height*@$w/$width;
            $startx=0;
            $starty=(@$h-$new_height)/2;
        }else{
            $new_height=@$h;
            $new_width=$width*@$h/$height;
            $startx=(@$w-$new_width)/2;
            $starty=0;

        }

        $col_red="0x".substr($bg,1,2)+0;
        $col_green="0x".substr($bg,3,2)+0;
        $col_blue="0x".substr($bg,5,2)+0;
        imagefill($image_p, 0, 0, imagecolorallocate($image_p, $col_red, $col_green, $col_blue));

    }

    imagecopyresampled($image_p, $image, $startx, $starty, $srcx, $srcy, $new_width, $new_height, $width, $height);

    switch($type){
        case IMAGETYPE_JPEG:
            imagejpeg($image_p, $dst, $q);
            break;
        case IMAGETYPE_GIF:
            imagegif($image_p,$dst);
            break;
        case IMAGETYPE_PNG:
            imagepng($image_p,$dst);
            break;
        default:
            break;
    }

    @chmod($dst,0777);
    @imagedestroy($image_p);
    @imagedestroy($image);

}
//конец функции makethumb




function sitemap(){
    global $mysql,$site_url;

    $sitemap[]="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $sitemap[]="<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";

    //страницы
    $query=@mysqli_query($mysql,"select * from `pages` where `visible`='1' and `published`='1' order by `pid` asc, `order` asc");
    while($row=@mysqli_fetch_assoc($query)){
        $sitemap[]="<url>";
        $sitemap[]="<loc>".$site_url.$row["rewrite"]."</loc>";
        $sitemap[]="<changefreq>monthly</changefreq>";
        $sitemap[]="<priority>0.5</priority>";
        $sitemap[]="</url>";
    }

    //новости
    $query=@mysqli_query($mysql,"select `news`.*,`pages`.`rewrite` as `catrewrite` from `news` left join `pages` on `pages`.`id`=`news`.`pid` where `pages`.`published`='1' and `news`.`visible`='1' and `news`.`published`='1' order by `news`.`adddate` desc");
    while($row=@mysqli_fetch_assoc($query)){
        $sitemap[]="<url>";
        $sitemap[]="<loc>".$site_url.$row["catrewrite"].$row["rewrite"]."/</loc>";
        $sitemap[]="<lastmod>".date("Y-m-d",strtotime($row["adddate"]))."</lastmod>";
        $sitemap[]="<changefreq>daily</changefreq>";
        $sitemap[]="<priority>0.7</priority>";
        $sitemap[]="</url>";
    }


    $sitemap[]="</urlset>";

    file_put_contents("../sitemap.xml",implode("\n",$sitemap));
}

//проверяем, есть ли доступ у пользователя админки к разделу
function check_user_access($_indexes){
    foreach ($_indexes as $index){
        if(@in_array($index,$_SESSION["admin_auth_user"]["role"])!==false)return TRUE;
    }
    return false;
}

//логирование в панели управления
function admin_log($action){
    global $mysql;

    @mysqli_query($mysql,"insert into `admin_log` (`user`,`action`) values ('".$_SESSION["admin_auth_user"]["id"]."','".@mysqli_real_escape_string($mysql,$action)."')");

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
