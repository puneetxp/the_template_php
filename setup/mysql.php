<?php

function mysql_table($table) {
    $x = [];
    foreach ($table['data'] as $item) {
        $x[] = $item['name'] . ' ' . $item['mysql_data'] . ' ' . $item['sql_attribute'];
    }
    // print_r($table);
    return 'CREATE TABLE ' . $table['table'] . '(' . implode(', ', $x) . ')ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;';
}

function migrate_table($table) {
    $relation_data = [];
    foreach ($table['data'] as $items) {
        if (isset($items['relations'])) {
            $relation_data[] = $items;
        }
    }
    if (count($relation_data) > 0) {
        $alter = "ALTER TABLE " . $table['table'];
        $relation_key_sql = $alter;
        $relation_constrain_sql = $alter;
        foreach ($relation_data as $id => $items) {
            foreach ($items['relations'] as $key => $value) {
                $relation_key_sql .= " ADD KEY " . $table['name'] . "_" . $value['name'] . "_foreign (`" . $value['name'] . "`)";
                $relation_constrain_sql .= " ADD CONSTRAINT " . $table['name'] . "_" . $value['name'] . "_foreign  FOREIGN KEY  (`" . $value['name'] . "`) REFERENCES " . $value['table'] . " (`" . $value['key'] . "`)";
            }
            if ((INT) $id + 1 == count($relation_data)) {
                $relation_key_sql .= ';';
                $relation_constrain_sql .= ';';
            } else {
                $relation_key_sql .= ',';
                $relation_constrain_sql .= ',';
            }
        }
        return $relation_key_sql . $relation_constrain_sql;
    }
}
