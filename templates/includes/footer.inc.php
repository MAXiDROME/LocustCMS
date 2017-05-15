<!-- Page Footer-->
<footer>
    <p><?=$_config["firm_name"]?> &copy; <?=date("Y", strtotime($_config["regdate"])) . "&mdash;" . date("Y")?></p>
</footer>
<!-- Java script-->
<script src="/js/scripts.js"></script>
<?
if (isset($_javascript_load)) {
    foreach ($_javascript_load as $js) {
        ?>
        <script src="<?=$js?>"></script>
        <?
    }
}
?>
<script>
    <?=@$_page_javascript?>
</script>
<!-- Coded by Locust-->
</body>
</html>