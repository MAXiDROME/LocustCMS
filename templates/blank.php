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
include_once 'includes/footer.inc.php';
