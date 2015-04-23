<?php

class ProductioncompanyformView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Build form
        $form = new ILARIA_ModuleFormbuilderForm(ProductioncompanyController::FORM_NAME, $data['model'], 'formbuilder');
        $form->setSettings(ILARIA_ConfigurationGlobal::buildRequestChain("productioncompany", $data['action'], array('prodid' => $data['prodid'])), "post");
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldSearch('company', 'Company', 4, 8, "search through companies...", 'productioncompany', 'searchcompanies'));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldSelect("type", "Type", 4, 8, $data['types']));
        $form->addComponent(new ILARIA_ModuleFormbuilderButtons("Validate", "Cancel", ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $data['prodid'])), 4, 5, 3));
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