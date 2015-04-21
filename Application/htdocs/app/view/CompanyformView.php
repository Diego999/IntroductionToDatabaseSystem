<?php

class CompanyformView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Build form
        $form = new ILARIA_ModuleFormbuilderForm(CompanyController::FORM_NAME, $data['model'], 'formbuilder');
        $form->setSettings(ILARIA_ConfigurationGlobal::buildRequestChain("company", $data['action'], array()), "post");
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("name", "Name", 4, 8, "company name", false, false, "company", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("country", "Country", 4, 8, "country code", false, true, "company", "text"));
        $target = (isset($data['id']) ?
            ILARIA_ConfigurationGlobal::buildRequestChain("company", "details", array('id' => $data['id'])) :
            ILARIA_ConfigurationGlobal::buildRequestChain("directaccess", "companies", array()));
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