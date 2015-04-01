<?php

class FrontendTemplate extends ILARIA_ApplicationTemplate
{
    public function display()
    {
        ?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>EPFL IMDB Project - Web application</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="description" content ="" />
        <meta name="keywords" content="" />
        <?php echo ILARIA_CoreLoader::getInstance()->includeStyle('global_frontend.css'); ?>
        <?php echo ILARIA_CoreLoader::getInstance()->includeStyle('bootstrap.css'); ?>
        <?php echo ILARIA_CoreLoader::getInstance()->includeStyle('bootstrap_theme.css'); ?>
        <?php echo ILARIA_CoreLoader::getInstance()->includeScript('jquery.js'); ?>
        <?php echo ILARIA_CoreLoader::getInstance()->includeScript('bootstrap.js'); ?>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row" id="row-header">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <img src="<?php echo ILARIA_CoreLoader::getInstance()->includeAsset('logo/app_logo.png'); ?>" alt="EPFL IMDB" id="img-title" />
                </div>
                <div class="col-md-8" id="cell-menu">
                    <?php ILARIA_ApplicationMenu::getMenu(MainMenu::MAIN_MENU_KEY)->display(); ?>
                </div>
                <div class="col-md-1"></div>
            </div>
            <?php
            if (count($this->view->getAlerts()) > 0)
            {
                echo "<div class=\"row\" id=\"row-alerts\">" . "\n";
                $count = 0;
                foreach ($this->view->getAlerts() as $alert)
                {
                    $type = $this->view->getAlertType($alert);
                    echo "<div class=\"col-md-5" . ($count%2==0 ? " col-md-offset-1" : "") . "\">" . "\n";
                    echo "<div class=\"alert alert-" . $type . " alert-dismissible\" role=\"alert\">" . "\n";
                    echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" . "\n";
                    echo $alert[ILARIA_ApplicationView::ALERT_MESSAGE] . "\n";
                    echo "</div>" . "\n";
                    echo "</div>" . "\n";
                    $count++;
                }
            echo "</div>" . "\n";
            }
            ?>
            <?php $this->view->display(); ?>
            <div class="row" id="row-footer">
                <div class="col-md-1"></div>
                <div class="col-md-7">
                    Developped by Diego Antognini, Jason Racine, Alexandre Veuthey (team 15) for the "Introduction to database systems" course
                </div>
                <div class="col-md-3" id="cell-ilaria">
                    Powered by <img src="<?php echo ILARIA_CoreLoader::getInstance()->includeAsset('logo/ILARIA_logo.png'); ?>" alt="ILARIA" id="img-footer" /> framework
                </div>
                <div class="col-md-1"></div>
            </div>
        </div>
    </body>
</html>
    <?php
    }
}