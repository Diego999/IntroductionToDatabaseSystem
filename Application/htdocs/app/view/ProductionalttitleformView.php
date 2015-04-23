<?php

class ProductionalttitleformView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Build form
        $form = new ILARIA_ModuleFormbuilderForm(ProductionController::FORM_ALTTITLE_NAME, $data['model'], 'formbuilder');
        $form->setSettings(ILARIA_ConfigurationGlobal::buildRequestChain("production", $data['action'], array('production_id' => $data['production_id'])), "post");
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("title", "Title", 4, 8, "alternative title", false, false, "name", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderButtons("Validate", "Cancel", ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $data['production_id'])), 4, 5, 3));
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