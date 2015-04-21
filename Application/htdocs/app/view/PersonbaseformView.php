<?php

class PersonbaseformView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Build form
        $form = new ILARIA_ModuleFormbuilderForm(PersonController::FORM_BASE_NAME, $data['model'], 'formbuilder');
        $form->setSettings(ILARIA_ConfigurationGlobal::buildRequestChain("person", $data['action'], array()), "post");
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("lastname", "Last name", 3, 3, "last name", false, false, "name", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("firstname", "First name", 3, 3, "first name", false, true, "name", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldSelect("gender", "Gender", 3, 3, array(
            array(
                ILARIA_ModuleFormbuilderFieldSelect::ELEM_KEY => "u",
                ILARIA_ModuleFormbuilderFieldSelect::ELEM_VAL => "-unknown-",
            ),
            array(
                ILARIA_ModuleFormbuilderFieldSelect::ELEM_KEY => "m",
                ILARIA_ModuleFormbuilderFieldSelect::ELEM_VAL => "Man",
            ),
            array(
                ILARIA_ModuleFormbuilderFieldSelect::ELEM_KEY => "f",
                ILARIA_ModuleFormbuilderFieldSelect::ELEM_VAL => "Woman",
            )
        )));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("birthdate", "Birthdate", 3, 3, "AAAA-MM-JJ", false, true, "person", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("deathdate", "Deathdate", 3, 3, "AAAA-MM-JJ", false, true, "person", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("birthname", "Birthname", 3, 3, "birthname", false, true, "person", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldArea("trivia", "Trivia", 3, 9, "short trivia", false, true, "person", 2));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldArea("quotes", "Quotes", 3, 9, "quotes", false, true, "person", 2));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldArea("minibiography", "Minibiography", 3, 9, "short and concise biography", false, true, "person", 5));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("spouse", "Spouse", 3, 3, "name of spouse", false, true, "person", "text"));
        $form->addComponent(new ILARIA_ModuleFormbuilderFieldInput("height", "Height", 3, 3, "height in meters", false, true, "person", "text"));
        $target = (isset($data['id']) ?
            ILARIA_ConfigurationGlobal::buildRequestChain("person", "details", array('id' => $data['id'])) :
            ILARIA_ConfigurationGlobal::buildRequestChain("directaccess", "persons", array()));
        $form->addComponent(new ILARIA_ModuleFormbuilderButtons("Validate", "Cancel", $target, 3, 5, 4));
        if (isset($data['id']))
        {
            $form->setDataId($data['id']);
        }

        // Output form
        $this->output("<div class=\"row row-pad-top-20\">");
        $this->output("<div class=\"col-md-6 col-md-offset-3\">");
        $this->output($form->display());
        $this->output("</div>");
        $this->output("</div>");
    }
}