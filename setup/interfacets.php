<?php

function interface_set($table)
{
   $x = [];
   foreach ($table['data'] as $item) {
      if (isset($item['sql_attribute']) && (str_contains($item['sql_attribute'], 'NOT NULL') || str_contains($item['sql_attribute'], 'PRIMARY') || str_contains($item['sql_attribute'], 'UNIQUE'))) {
         $x[] = $item['name'] . ': ' . $item['datatype'];
      } else {
         $x[] = $item['name'] . ': ' . $item['datatype'] . ' | null';
      }
   }
   return "export interface " . ucfirst($table['name']) . " {
   " . implode(',
   ', $x) . '
}';
}
