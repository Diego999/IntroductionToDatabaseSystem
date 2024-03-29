<?php

class PersonrolesseriesAsynchronous extends ILARIA_ApplicationAsynchronous
{
    protected function getUniqueIdentifier()
    {
        return "personrolesseries";
    }

    protected function getWebPath($params)
    {
        return ILARIA_ConfigurationGlobal::buildRequestChain("person", "asyncrolesseries", $params);
    }

    protected function getDisplayStructure($params)
    {
        return "<table class=\"table\" id=\"" . $this->getContainerId() . "\">"
            . "<tr><th>Role</th><th>Character</th><th>Title</th><th>Years</th><th>Genre</th><th>Appearances</th></tr>"
            . "<tr id=\"" . $this->getLoadingId() . "\"><td colspan=\"6\" style=\"text-align:center\">" . $this->getLoadingGif() . "</td></tr>"
            . "</table>";
    }

    protected function getDisplayRow()
    {
        return "<tr class=\\\"" . $this->getElementClass() . "\\\">"
            . "<td>:role_name</td>"
            . "<td>:char_name</td>"
            . "<td><a class=\\\"btn btn-primary btn-xs\\\" href=\\\"" . ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => ':prod_id')) . "\\\" role=\\\"button\\\"><span class=\\\"glyphicon glyphicon-arrow-right\\\" aria-hidden=\\\"true\\\"></span></a> :prod_title</td>"
            . "<td>:prod_yearstart-:prod_yearend</td>"
            . "<td>:prod_gender</td>"
            . "<td>:episode_count episodes in :season_count seasons</td>"
            . "</tr>";
    }

    protected function getDisplayError()
    {
        return "<tr>"
        . "<td colspan=\\\"6\\\" style=\\\"text-align:center; font-weight: bold; font-color: #0066ff\\\">:error</td>"
        . "</tr>";
    }

    protected function getDisplayPaginator()
    {
        return "<div>"
        . "<h3 class=\"panel-title\">Played in series</h3></div>"
        . "<div style=\"float: right; margin-top:-25px;\">"
        . "<table><tr>"
        . "<td><a id=\"" . $this->getPaginatorText() . "\" style=\"margin-right:20px\">Loading...</a></td>"
        . "<td><nav style=\"visibility:hidden\" id=\"" . $this->getPaginatorId() . "\">"
        . "<ul class=\"pagination\">"
        . "<li id=\"" . $this->getPaginatorPreviousButton() . "\" " . $this->getPaginatorOnClickPrevious() . ">"
        . "<a style=\"cursor:pointer\" aria-label=\"Previous\"><span aria-hidden=\"true\">&laquo;</span></a>"
        . "</li>"
        . "<li id=\"" . $this->getPaginatorNextButton() . "\" " . $this->getPaginatorOnClickNext() . ">"
        . "<a style=\"cursor:pointer\" aria-label=\"Next\"><span aria-hidden=\"true\">&raquo;</span></a>"
        . "</li>"
        . "</ul>"
        . "</nav></td></tr></table></div>";
    }

    protected function getDisplayPaginatorButton()
    {
        return "<li class=\\\"" . $this->getPaginatorButtonClass() . "\\\"><a style=\\\"cursor:pointer\\\" " . $this->getPaginatorOnClickNumeric() . ">:num</a></li>";
    }

    protected function getDisplayPaginatorButtonActive()
    {
        return "<li class=\\\"" . $this->getPaginatorButtonClass() . " active\\\"><a " . $this->getPaginatorOnClickNumeric() . ">:num <span class=\\\"sr-only\\\">(current)</span></a></li>";
    }

    protected function getRawContent($params)
    {
        $model = $this->getModel("person");
        $content = $model->getPersonInfosRolesSeries($params['person_id']);
        if (!is_array($content))
        {
            throw new ILARIA_CoreError("An error occurred while searching through the series",
                ILARIA_CoreError::GEN_ASYNC_QUERY_FAILED,
                ILARIA_CoreError::LEVEL_ADMIN);
        }
        for ($i=0; $i<count($content); $i++)
        {
            $content[$i]['prod_yearstart'] = ($content[$i]['prod_yearstart'] ? $content[$i]['prod_yearstart'] : "?");
            $content[$i]['prod_yearend'] = ($content[$i]['prod_yearend'] ? $content[$i]['prod_yearend'] : "?");
        }
        return $content;
    }
}