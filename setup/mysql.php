<?php

class mysqltable
{
    public static function addattribute($tables)
    {
        return array_map(fn ($item) => array_replace(
            $item,
            ["data" => array_map(fn ($data) =>
            array_replace(
                $data,
                ["sql_attribute" => ((isset($data['default']) || isset($data['sql_attribute'])) ? ((isset($data['default']) ?
                    ($data['default'] === "NULL" ? "" : " NOT NULL ") . " DEFAULT " . $data["default"] :
                    '')
                    . " " . (isset($data["sql_attribute"]) ? $data["sql_attribute"] : ''))
                    : " NOT NULL ")]
            ), $item['data'])]
        ), $tables);
    }
    public static function table($table)
    {
        return 'CREATE TABLE ' . $table['table'] . '(' .
            implode("", array_map(
                fn ($item) =>
                "`" . $item['name'] . "`" . ' ' .
                    $item['mysql_data'] . ' ' .
                    $item['sql_attribute'],
                $table['data']
            )) . ')ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8;';
    }
    public static function migrate_table($table)
    {
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
                foreach ($items['relations'] as $value) {
                    $relation_key_sql .= " ADD KEY " . $table['name'] . "_" . $value['name'] . "_foreign (`" . $value['name'] . "`)";
                    $relation_constrain_sql .= " ADD CONSTRAINT " . $table['name'] . "_" . $value['name'] . "_foreign  FOREIGN KEY  (`" . $value['name'] . "`) REFERENCES " . $value['table'] . " (`" . $value['key'] . "`)";
                }
                if ((int) $id + 1 == count($relation_data)) {
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
}