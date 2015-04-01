<?php

class ILARIA_DatabaseQuery
{
    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $sql = NULL;
    private $status = NULL;
    private $count = NULL;
    private $fields = NULL;
    private $data = NULL;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct($sql) { $this->sql = $sql; }

    public function getSql() { return $this->sql; }
    public function getStatus() { return $this->status; }
    public function getCount() { return $this->count; }
    public function getFields() { return $this->fields; }
    public function getData() { return $this->data; }

    public function setStatus($status) { $this->status = $status; }
    public function setCount($count) { $this->count = $count; }
    public function setFields($fields) { $this->fields = $fields; }
    public function setData($data) { $this->data = $data; }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_DatabaseQuery.php] class loaded');