<?php

function interface_set($table)
{
   $x = [];
   foreach ($table['data'] as $item) {
      if (str_contains($item['sql_attribute'], 'NOT NULL') || str_contains($item['sql_attribute'], 'PRIMARY')) {
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
