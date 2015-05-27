<?php
class Database
{
    private $pdo = null;
    private $params;
    private $result;
    
    public function __construct($params)
    {
        $this->params = $params;
        try
        {
            $this->pdo = new PDO($params[0], $params[1], $params[2], array( PDO::ATTR_PERSISTENT => false));
        } 
        catch(Exception $e)
        {
            print_r($e->getMessage());
        }
    }
    
    public function getResult()
    {
        return $this->result;
    }
    
    public function fetch($sql, $fetch_method = PDO::FETCH_ASSOC)
    {
        try
        {
            $this->result = $this->pdo->query($sql)->fetch($fetch_method);
        }
        catch(Exception $e)
        {
            print_r($e->getMessage());
        }
    }
    
    public function fetchAll($sql, $fetch_method = PDO::FETCH_ASSOC)
    {
        try
        {
            $this->result = $this->pdo->query($sql)->fetchAll($fetch_method);
        }
        catch(Exception $e)
        {
            print_r($e->getMessage());
        }
    }
    
    public function sql($sql)
    {
        try
        {
            $this->result = $this->pdo->prepare($sql)->execute();
        }
        catch(Exception $e)
        {
            print_r($e->getMessage());
        }
    }
}