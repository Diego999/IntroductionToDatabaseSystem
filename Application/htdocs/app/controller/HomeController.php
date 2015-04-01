<?php

class HomeController extends ILARIA_ApplicationController
{
    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'index':
                return true;
            default:
                return false;
        }
    }

    public function action_index($request)
    {
        // Create view
        $view = $this->getView('homeindex');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_HOME);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Create an alert
        /*
        $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "tout fonctionne à merveille");
        $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_INFO, "j'ai une information cruciale pour vous (et ce texte est vraiment beaucoup trop long, on va voir ce que ça fait... et bla et bla et bla bla bla");
        $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_WARNING, "un avertissement est à prendre au sérieux");
        $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "ce n'est pas l'objectif qui est trop haut, c'est l'échelle qui est trop courte !");
        */

        // Output to view
        $view->prepare(array());

        // Return view
        return $view;
    }
}
