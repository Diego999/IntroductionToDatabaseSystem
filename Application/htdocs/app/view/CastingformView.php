<?php

class CastingformView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Build form
        $form = new ILARIA_ModuleFormbuilderForm(CastingController::FORM_NAME, $data['model'], 'formbuilder');
        $form->setSettings(ILARIA_ConfigurationGlobal::buildRequestChain("casting", $data['action'], array('prodid' => $data['prodid'])), "post");
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldSearch('person', 'Person', 4, 8, "search through persons...", 'casting', 'searchpersons'));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldSelect("role", "Role", 4, 8, $data['roles']));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("character", "Character", 4, 8, "character played", false, true, "character", "text"));
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