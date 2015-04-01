<?php

class HttpTemplate extends ILARIA_ApplicationTemplate
{
    public function display()
    {
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Default HTTP Template</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="description" content ="" />
        <meta name="keywords" content="" />
    </head>
    <body>
        <h2>Default HTTP template</h2>
        <?php $this->view->display(); ?>
        <h3>The menu :</h3>
        <?php ILARIA_ApplicationMenu::getMenu(BaseMenu::BASE_MENU_KEY)->display(); ?>
    </body>
</html>
<?php
    }
}