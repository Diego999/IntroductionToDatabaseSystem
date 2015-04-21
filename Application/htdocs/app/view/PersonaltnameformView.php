<?php

class PersonaltnameformView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Build form
        $form = new ILARIA_ModuleFormbuilderForm(PersonController::FORM_ALTNAME_NAME, $data['model'], 'formbuilder');
        $form->setSettings(ILARIA_ConfigurationGlobal::buildRequestChain("person", $data['action'], array('person_id' => $data['person_id'])), "post");
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("lastname", "Lastname", 4, 8, "last name", false, false, "name", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("firstname", "Firstname", 4, 8, "first name", false, true, "name", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderButtons("Validate", "Cancel", ILARIA_ConfigurationGlobal::buildRequestChain("person", "details", array('id' => $data['person_id'])), 4, 5, 3));
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