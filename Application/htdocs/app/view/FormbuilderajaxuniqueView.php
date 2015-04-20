<?php

class FormbuilderajaxuniqueView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        $this->output($data['isunique']);
    }
}