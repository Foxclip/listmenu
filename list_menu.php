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

$mysql_ini = parse_ini_file("mysql.ini");
$db_name = $mysql_ini["database_name"];
$table_name = $mysql_ini["table_name"];
$pdo = mysql\db_connect();

// получения отступа из строки пути, которя пришла из базы
function get_indent(string $path): string {
    $arr = explode("/", $path);
    $parent_count = count($arr) - 1;
    $result = "";
    for ($i = 0; $i < $parent_count; $i++) {
        $result .= "    ";
    }
    return $result;
}

// получение списка меню
// можно указать максимальную глубину вложенности
function query_items(int $max_depth): array {
    global $db_name;
    global $table_name;
    global $pdo;

    $sql = <<<SQL
    WITH RECURSIVE
        temp(id, name, url, path, depth) AS (
            SELECT
                id,
                name,
                CAST(CONCAT('/', alias) AS CHAR(200)),
                CAST(LPAD(id, 3, '0') AS CHAR(200)),
                0
            FROM $table_name
            WHERE parent_id IS NULL
            UNION ALL
            SELECT
                $table_name.id,
                $table_name.name,
                CONCAT('/', TRIM(BOTH '/' FROM temp.url), '/', $table_name.alias),
                CONCAT(temp.path, '/', LPAD($table_name.id, 3, '0')),
                depth + 1
            FROM temp JOIN $table_name ON temp.id = $table_name.parent_id
        )
    SELECT * FROM temp
    WHERE depth < :hierarchy_depth
    ORDER BY path;
    SQL;
    
    $pdo->exec("USE $db_name;");
    $statement = $pdo->prepare($sql);
    $statement->bindValue(":hierarchy_depth", $max_depth);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// получаем весь список из базы
$items_all = query_items(max_depth: 1000);

// выводим названия пунктов меню на страницу
write_html("<pre>");
foreach ($items_all as $row) {
    $indent = get_indent($row["path"]);
    write_text($indent);
    write_text($row["name"]);
    write_line();
}
write_html("</pre>");

// выводим названия пунктов меню и url в type_a.txt
$type_a_str = "";
foreach ($items_all as $row) {
    $indent = get_indent($row["path"]);
    $type_a_str .= $indent;
    $type_a_str .= $row["name"];
    $type_a_str .= " ";
    $type_a_str .= $row["url"];
    $type_a_str .= "\n";
}
file_put_contents("type_a.txt", $type_a_str);

// получаем первые два уровня списка из базы
$items_first_level = query_items(max_depth: 2);

// выводим названия пунктов меню (первые два уровня) в type_b.txt
$type_b_str = "";
foreach ($items_first_level as $row) {
    $indent = get_indent($row["path"]);
    $type_b_str .= $indent;
    $type_b_str .= $row["name"];
    $type_b_str .= "\n";
}
file_put_contents("type_b.txt", $type_b_str);

?>

</body>
</html>
