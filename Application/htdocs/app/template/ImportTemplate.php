<?php

class ImportTemplate extends ILARIA_ApplicationTemplate
{
    public function display()
    {
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>EPFL IMDB Project - Importation tool</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="description" content ="" />
        <meta name="keywords" content="" />
        <?php echo ILARIA_CoreLoader::getInstance()->includeStyle('global_import.css'); ?>
    </head>
    <body>
        <div id="main-container">
            <div id="header">
                <img src="<?php echo ILARIA_CoreLoader::getInstance()->includeAsset('logo/app_logo.png'); ?>" alt="EPFL IMDB" />
                <div id="header-title">
                    Importation tool
                </div>
            </div>
            <div id="content">
                <?php $this->view->display(); ?>
            </div>
            <div id="footer">
                <img src="<?php echo ILARIA_CoreLoader::getInstance()->includeAsset('logo/ILARIA_logo.png'); ?>" alt="ILARIA" />
            </div>
        </div>
    </body>
</html>
<?php
    }
}