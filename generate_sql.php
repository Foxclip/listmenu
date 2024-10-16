<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            height: fit-content;
        }
        body {
            display: flow-root;
            font-family: sans-serif;
        }
    </style>

<?php

require_once "writer.php";
require_once "mysql.php";
use function writer\write_html;
use function writer\write_text;
use function writer\write_line;
use function writer\write_tag_text;

const OUTPUT_SQL_FILE = "create_listmenu.sql";

$pdo = \mysql\db_connect();
$json = file_get_contents("categories.json");
$list_items = json_decode($json);
$mysql_ini = parse_ini_file("mysql.ini");
$db_name = $mysql_ini["database_name"];
$table_name = $mysql_ini["table_name"];
$sql_output = "";

write_line("Создание базы, таблицы и их заполнение:");

write_html("<pre>");

// CREATE DATABASE
$sql_db_create = "DROP DATABASE IF EXISTS $db_name;
CREATE DATABASE $db_name;
USE $db_name;
";

// CREATE TABLE
$sql_table_create = "DROP TABLE IF EXISTS $table_name;
CREATE TABLE $table_name(
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL,
    alias VARCHAR(20) NOT NULL,
    parent_id INT,
    FOREIGN KEY (parent_id) REFERENCES $table_name(id)
);
";
$sql_output .= $sql_db_create."\n";
$sql_output .= $sql_table_create."\n";
write_line($sql_db_create);
write_line($sql_table_create);

// INSERT
$add_insert = function($obj, $parent) use(&$pdo, &$sql_output, $table_name, &$add_insert): void {
    $id = $obj->id;
    $quoted_name = $pdo->quote($obj->name);
    $quoted_alias = $pdo->quote($obj->alias);
    $parent_id = $parent ? $parent->id : "NULL";
    $sql_insert = "INSERT INTO $table_name VALUES ($id, $quoted_name, $quoted_alias, $parent_id);";
    $sql_output .= $sql_insert."\n";
    write_line($sql_insert);
    if (isset($obj->childrens)) {
        foreach ($obj->childrens as $child)  {
            $add_insert($child, $obj);
        }
    }
};
foreach ($list_items as $root_item) {
    $add_insert($root_item, null);
}
file_put_contents(OUTPUT_SQL_FILE, $sql_output);

write_html("</pre>");

?>

</body>
</html>
