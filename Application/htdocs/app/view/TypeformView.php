<?php

class TypeformView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Build form
        $form = new ILARIA_ModuleFormbuilderForm(TypeController::FORM_NAME, $data['model'], 'formbuilder');
        $form->setSettings(ILARIA_ConfigurationGlobal::buildRequestChain("type", $data['action'], array()), "post");
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("name", "Type", 4, 8, "name of type", true, false, "type", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderButtons("Validate", "Cancel", ILARIA_ConfigurationGlobal::buildRequestChain("directaccess", "miscellaneous", array()), 4, 5, 3));
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