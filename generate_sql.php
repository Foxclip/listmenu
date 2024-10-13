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

use function writer\write_html;
use function writer\write_line;
use function writer\write_tag_text;

require_once "writer.php";
require_once "mysql.php";

const DB_NAME = "ListMenuDb";
const TABLE_NAME = "ListItems";

$pdo = \mysql\db_connect();
$json = file_get_contents("categories.json");
$list_items = json_decode($json);
write_html("<pre>");
$sql_db_create = "DROP DATABASE IF EXISTS ".DB_NAME.";
CREATE DATABASE ".DB_NAME.";
USE ".DB_NAME.";
";
$sql_table_create = "DROP TABLE IF EXISTS ".TABLE_NAME.";
CREATE TABLE ListItems(
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20),
    alias VARCHAR(20),
    parent_id INT,
    FOREIGN KEY (parent_id) REFERENCES ".TABLE_NAME."(id)
);
";
write_line($sql_db_create);
write_line($sql_table_create);
$add_insert = function($obj, $parent) use(&$pdo, &$add_insert): void {
    $id = $obj->id;
    $quoted_name = $pdo->quote($obj->name);
    $quoted_alias = $pdo->quote($obj->alias);
    $parent_id = $parent ? $parent->id : "NULL";
    $sql_insert = "INSERT INTO ListItems VALUES ($id, $quoted_name, $quoted_alias, $parent_id);";
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
write_html("</pre>");

?>

</body>
</html>
