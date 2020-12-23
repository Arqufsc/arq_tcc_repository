<?php
class dbMysqlConnection
{
    private $credentialsFile;
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_pswd;
    private $connection;
    
    public function __construct()
    {
        $this->setCredentialsFile();
        $this->getCredentials();
        $this->setConnection();
    }

    private function setCredentialsFile()
    {
        if($_SERVER['SERVER_NAME']=='localhost')
            $this->credentialsFile = "../config/dbCredentialsLocal.json";
        else
            $this->credentialsFile = "../config/dbCredentialsRemote.json";
    }

    private function getCredentials()
    {
        foreach(Files::readDataStructure($this->credentialsFile) as $var=>$value)
            $this->$var = $value;
    }

    private function setConnection()
    {
        $dsn = "mysql:dbname={$this->db_name};host={$this->db_host}";
        $this->connection = new PDO($dsn, $this->db_user, $this->db_pswd);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection()
    {
        return $this->connection;
    }
}