<?php

abstract class BaseModel
{
    protected $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    abstract public function find($id);
    abstract public function all();
    abstract public function create($data);
    abstract public function update($id, $data);
    abstract public function delete($id);
}

?>
