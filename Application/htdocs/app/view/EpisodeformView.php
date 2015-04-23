<?php

class EpisodeformView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Build form
        $form = new ILARIA_ModuleFormbuilderForm(EpisodeController::FORM_NAME, $data['model'], 'formbuilder');
        $validTarget = (isset($data['id']) ?
            array() :
            array('serieid' => $data['serieid']));
        $form->setSettings(ILARIA_ConfigurationGlobal::buildRequestChain("episode", $data['action'], $validTarget), "post");
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("title", "Title", 4, 8, "main title", false, false, "title", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("year", "Year", 4, 8, "year of distribution", false, true, "production", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("season_number", "Season #", 4, 8, "season number", false, true, "season", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("episode_number", "Episode #", 4, 8, "episode number", false, true, "episode", "text"));
        $target = (isset($data['id']) ?
            ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $data['id'])) :
            ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $data['serieid'])));
        $form->addComponent(new ILARIA_ModuleFormbuilderButtons("Validate", "Cancel", $target, 4, 5, 3));
        if (isset($data['id']))
        {
            $form->setDataId($data['id']);
        }

        // Output form
        $this->output("<div class=\"row row-pad-top-20\">");
        $this->output("<div class=\"col-md-4 col-md-offset-4\">");
        $this->output($form->display());
        $this->output("</div>");
        $this->output("</div>");
    }
}