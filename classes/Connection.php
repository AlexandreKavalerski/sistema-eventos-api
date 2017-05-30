<?php
define ("SERVERNAME", "");
define ("DATABASE_NAME", "");
define("USERNAME", "");
define("PASSWORD", "");
/*
define("USERNAME", "");
define("PASSWORD", "");
*/

class Connection
{
    public static function Open()
    {
        try
        {
            $conn = new PDO("mysql:host=".SERVERNAME.";dbname=".DATABASE_NAME, USERNAME, PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
            $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch(PDOException $ex)
        {
            throw new Exception ($ex -> getMessage());
        }

    }
}
?>