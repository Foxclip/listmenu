<?php

namespace writer;

function write_text(mixed $text = ""): void {
    echo htmlspecialchars($text);
}

function write_line(mixed $text = ""): void {
    echo write_text($text)."<br>";
}

function write_html(string $text): void {
    echo $text;
}

function write_tag_text(string $tag, mixed $text): void {
    write_html("<$tag>");
    write_text($text);
    write_html("</$tag>");
}

function write_p(mixed $text): void {
    write_tag_text("p", $text);
}

function write_table(iterable $column_names, iterable $rows): void {
    write_html("<table>");
    write_html("<tr>");
    foreach ($column_names as $col) {
        write_tag_text("th", $col);
    }
    write_html("</tr>");
    foreach ($rows as $row) {
        write_html("<tr>");
        foreach ($row as $val) {
            write_tag_text("td", $val);
        }
        write_html("</tr>");
    }
    write_html("</table>");
}
?>
