<?php
spl_autoload_register(function($className) {
    if(file_exists(dirname(__FILE__)."/classes/".$className . '.php'))
    {
        include_once dirname(__FILE__)."/classes/".$className . '.php';
    }
});
?>