<?php

class PersondetailsView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Left margin
        $this->output("<div class=\"row row-pad-top-20\">");

        // Person name
        $this->output("<div class=\"col-md-5 col-md-offset-1\">");
        $this->output("<h2>" . $data['infos']['firstname'] . " " . $data['infos']['lastname'] . "</h2>");
        $this->output("</div>");

        // SCUD buttons
        $this->output("<div class=\"col-md-5\" style=\"text-align:right\">");
        $this->output("<a class=\"btn btn-danger btn-md\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("person", "update", array('id' => $data['infos']['id'])) . "\" role=\"button\"><span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> update</a>");
        $this->output("<a class=\"btn btn-danger btn-md\" href=\"#\" role=\"button\" " . ILARIA_ApplicationAsynchronous::getModalOnClickShow(ILARIA_ConfigurationGlobal::buildRequestChain('person', 'delete', array('id' => $data['infos']['id'])), false) . "><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span> delete</a>");
        $this->output("</div>");

        $this->output("</div>");
        $this->output("<div class=\"row\">");

        // Begin left panel
        $this->output("<div class=\"col-md-3 col-md-offset-1\">");

        // Basic infos tab
        $this->output("<table class=\"table table-striped\">");
        switch ($data['infos']['gender'])
        {
            case 'm':
                $gender = "Man";
                break;
            case 'f':
                $gender = "Woman";
                break;
            default:
                $gender = "-";
                break;
        }
        $this->output("<tr><td>Gender</td><td>" . $gender . "</td></tr>");
        $this->output("<tr><td>Birthdate</td><td>" . ($data['infos']['birthdate'] ? $data['infos']['birthdate'] : "-") . "</td></tr>");
        $this->output("<tr><td>Deathdate</td><td>" . ($data['infos']['deathdate'] ? $data['infos']['deathdate'] : "-") . "</td></tr>");
        $this->output("<tr><td>Birthname</td><td>" . ($data['infos']['birthname'] ? $data['infos']['birthname'] : "-") . "</td></tr>");
        $this->output("<tr><td>Spouse</td><td>" . ($data['infos']['spouse'] ? $data['infos']['spouse'] : "-") . "</td></tr>");
        $this->output("<tr><td>Height</td><td>" . ($data['infos']['height'] ? $data['infos']['height'] . " m" : "-") . "</td></tr>");
        $this->output("</table>");

        // Alternative names
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asyncaltnames']->getPaginator());
        $this->output("</div>");
        $this->output($data['asyncaltnames']->getStructure(array(
            'person_id' => $data['infos']['id'],
            'mainname_id' => $data['infos']['name_id'],
        )));
        $this->output("</div>");

        // Trivia
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Trivia</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output($data['infos']['trivia'] ? nl2br($data['infos']['trivia']) : "-");
        $this->output("</div></div>");

        // Quotes
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Quotes</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output($data['infos']['quotes'] ? nl2br($data['infos']['quotes']) : "-");
        $this->output("</div></div>");

        // Begin right panel
        $this->output("</div>");
        $this->output("<div class=\"col-md-7\">");

        // Biography
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Biography</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output($data['infos']['minibiography'] ? nl2br($data['infos']['minibiography']) : "-");
        $this->output("</div></div>");

        // Movies
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asyncrolessingle']->getPaginator());
        $this->output("</div>");
        $this->output($data['asyncrolessingle']->getStructure(array(
            'person_id' => $data['infos']['id'],
        )));
        $this->output("</div>");

        // Series
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asyncroleseries']->getPaginator());
        $this->output("</div>");
        $this->output($data['asyncroleseries']->getStructure(array(
            'person_id' => $data['infos']['id'],
        )));
        $this->output("</div>");

        // End right panel
        $this->output("</div>");

        // Right margin
        $this->output("</div>");
    }
}