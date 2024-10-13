<?php

namespace mysql;

require_once "writer.php";
use function writer\write_line;

const INI_FILENAME = "mysql.ini";

function get_col_names(\PDOStatement $statement) : array {
    $columns = [];
    for ($i = 0; $i < $statement->columnCount(); $i++) {
        $col_name = $statement->getColumnMeta($i)["name"];
        $columns[] = $col_name;
    }
    return $columns;
}

function db_connect() : \PDO {
    $mysql_ini = parse_ini_file(INI_FILENAME);
    if (!$mysql_ini) {
        throw new \Exception("Ini file error");
    }
    $host = $mysql_ini["host"];
    $user = $mysql_ini["user"];
    $password = $mysql_ini["password"];
    $dbstr = "";
    if (isset($mysql_ini["database"])) {
        $dbstr = ";dbname=".$mysql_ini["database"];
    }
    $conn = new \PDO("mysql:host=$host$dbstr", $user, $password);
    return $conn;
}
?>
