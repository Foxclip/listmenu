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

function query_items(int $max_depth): array {
    $sql = <<<SQL
    WITH RECURSIVE
        temp(id, name, url, path, depth) AS (
            SELECT
                id,
                name,
                CAST(CONCAT('/', alias) AS CHAR(200)),
                CAST(LPAD(id, 3, '0') AS CHAR(200)),
                0
            FROM ListItems
            WHERE parent_id IS NULL
            UNION ALL
            SELECT
                ListItems.id,
                ListItems.name,
                CONCAT('/', TRIM(BOTH '/' FROM temp.url), '/', ListItems.alias),
                CONCAT(temp.path, '/', LPAD(ListItems.id, 3, '0')),
                depth + 1
            FROM temp JOIN ListItems ON temp.id = ListItems.parent_id
        )
    SELECT * FROM temp
    WHERE depth < :hierarchy_depth
    ORDER BY path;
    SQL;
    $pdo = mysql\db_connect();
    $pdo->exec("USE ".DB_NAME.";");
    $statement = $pdo->prepare($sql);
    $statement->bindValue(":hierarchy_depth", $max_depth);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

$items_all = query_items(1000);

write_html("<pre>");
foreach ($items_all as $row) {
    $parents = count_parents($row["path"]);
    $indent = get_indent($parents);
    write_text($indent);
    write_val($row["name"]);
    write_line();
}
write_html("</pre>");

$type_a_str = "";
foreach ($items_all as $row) {
    $parents = count_parents($row["path"]);
    $indent = get_indent($parents);
    $type_a_str .= $indent;
    $type_a_str .= $row["name"];
    $type_a_str .= " ";
    $type_a_str .= $row["url"];
    $type_a_str .= "\n";
}
file_put_contents("type_a.txt", $type_a_str);

$items_first_level = query_items(2);

$type_b_str = "";
foreach ($items_first_level as $row) {
    $parents = count_parents($row["path"]);
    $indent = get_indent($parents);
    $type_b_str .= $indent;
    $type_b_str .= $row["name"];
    $type_b_str .= "\n";
}
file_put_contents("type_b.txt", $type_b_str);

?>

</body>
</html>
