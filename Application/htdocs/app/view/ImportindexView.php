<?php

class ImportindexView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        foreach($data as $line)
        {
            $this->output($line . "<br />");
        }
    }
}