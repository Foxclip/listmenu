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

const DB_NAME = "ListMenuDb";
const INPUT_SQL_FILE = "get_listmenu.sql";

function write_val($val): void {
    write_text(is_null($val) ? "NULL" : $val);
}
function count_parents(string $path): int {
    $arr = explode("/", $path);
    return count($arr) - 1;
}
function get_indent(int $depth): string {
    $result = "";
    for ($i = 0; $i < $depth; $i++) {
        $result .= "    ";
    }
    return $result;
}

$sql = file_get_contents(INPUT_SQL_FILE);
$pdo = mysql\db_connect();
$pdo->exec("USE ".DB_NAME.";");
$result = $pdo->query($sql, PDO::FETCH_ASSOC);

write_html("<pre>");
foreach ($result as $row) {
    $parents = count_parents($row["path"]);
    $indent = get_indent($parents);
    write_text($indent);
    write_val($row["name"]);
    write_text(" ");
    write_val($row["url"]);
    write_line();
}
write_html("</pre>");

?>

</body>
</html>
