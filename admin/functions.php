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