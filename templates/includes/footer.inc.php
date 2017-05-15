<!-- Page Footer-->
<footer>
    <p><?=$_config["firm_name"]?> &copy; <?=date("Y", strtotime($_config["regdate"])) . "&mdash;" . date("Y")?></p>
</footer>
<!-- Java script-->
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
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