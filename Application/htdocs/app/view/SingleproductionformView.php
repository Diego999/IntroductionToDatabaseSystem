<?php

class SingleproductionformView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Build form
        $form = new ILARIA_ModuleFormbuilderForm(SingleproductionController::FORM_NAME, $data['model'], 'formbuilder');
        $form->setSettings(ILARIA_ConfigurationGlobal::buildRequestChain("singleproduction", $data['action'], array()), "post");
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("title", "Title", 4, 8, "main title", false, false, "title", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("year", "Year", 4, 8, "year of distribution", false, true, "production", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldSelect("gender", "Genre", 4, 8, $data['genders']));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldSelect("kind", "Kind", 4, 8, $data['kinds']));
        $target = (isset($data['id']) ?
            ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $data['id'])) :
            ILARIA_ConfigurationGlobal::buildRequestChain("directaccess", "productions", array()));
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