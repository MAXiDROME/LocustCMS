<?
include_once 'includes/header.inc.php';

include_once 'includes/page.header.inc.php';
?>
    <main>
        <?
        include_once 'includes/breadcrumbs.inc.php';
        ?>
        <section>
            <h1><?=$_page["title"]?></h1>
            <?=$_page["content"]?>
        </section>
    </main>
<?

//javascript loader example
//$_javascript_load[]="/js/jquery-ui.min.js";
//$_javascript_load[]="/js/jquery.iframe-transport.js";
//$_javascript_load[]="/js/jquery.fileupload.js";
//$_page_javascript="alert('hello!');";

include_once 'includes/footer.inc.php';
