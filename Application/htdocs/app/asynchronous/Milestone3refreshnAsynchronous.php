<?php

class Milestone3refreshnAsynchronous extends ILARIA_ApplicationAsynchronous
{
    protected function getUniqueIdentifier()
    {
        return "milestone3refreshn";
    }

    protected function getWebPath($params)
    {
        return ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "refreshn", $params);
    }

    protected function getDisplayStructure($params)
    {
        return "<table class=\"table\" id=\"" . $this->getContainerId() . "\">"
        . "<tr><th>Status</th></tr>"
        . "<tr id=\"" . $this->getLoadingId() . "\"><td colspan=\"1\" style=\"text-align:center\">" . $this->getLoadingGif() . "</td></tr>"
        . "</table>";
    }

    protected function getDisplayRow()
    {
        return "<tr class=\\\"" . $this->getElementClass() . "\\\">"
        . "<td>:status</td>"
        . "</tr>";
    }

    protected function getDisplayError()
    {
        return "<tr>"
        . "<td colspan=\\\"1\\\" style=\\\"text-align:center; font-weight: bold; font-color: #0066ff\\\">:error</td>"
        . "</tr>";
    }

    protected function getDisplayPaginator()
    {
        return "<div>"
        . "<h3 class=\"panel-title\">Most frequent character per country - refreshing materialized view</h3></div>"
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
        $model = $this->getModel("milestone3");
        $status = $model->refreshN();
        $content = array(
            array(
                'status' => ($status == 0 ? 'finished' : 'error')
            )
        );
        return $content;
    }
}