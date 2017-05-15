<?php

include_once 'config.php';

include_once 'functions.php';

include_once 'functions.conditions.php';

include_once 'route.php';

if($_config["maintenance"]==1)$_page["template"]="maintenance";

include_once 'templates/'.$_page["template"].'.php';