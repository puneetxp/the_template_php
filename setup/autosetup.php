<?php

$pattern_route = '/\$route.*?;/';
$pattern_use_only = '/use.*?\w;/';
$pattern_use_multple = "/use (.*?){(.*?)};/";
$route_use_single = '';
$route_use_array = [];
$route_use_array['The\\'] = ["Route"];
$route_use_multiple = '';
$route_app = ' (new Route())';
$json_set = json_decode(file_get_contents(__DIR__ . '/../config.json'), TRUE);

foreach (glob(__DIR__ . "/*.php") as $filename) {
   require_once $filename;
}
$x = [];
foreach (glob(__DIR__ . "/model/*.json") as $file) {
   $filename = preg_replace("/.*.\/(.*).json/", "$1", $file);
   $j = json_decode(file_get_contents($file), TRUE);
   if (isset($j['alter'])) {
   } else {
      $x[$filename] = $j;
   }
}
$table = [];
$roles = ['isuper'];
foreach ($x as $key => $item) {
   if (isset($item['crud']['roles'])) {
      if (is_array($item['crud']['roles'])) {
         $roles = array_merge(array_keys($item['crud']['roles']), $roles);
      }
   }
   $table[] = table_set($item, array_values($x));
}

$roles = array_filter(array_unique($roles), fn ($role) => !($role == "*" || $role == "-"));
for ($i = 0; $i < count($table); ++$i) {
   if (count($table[$i]['relations']) > 0) {
      foreach ($table[$i]['relations'] as $key => $items) {
         for ($t = 0; $t < count($table); ++$t) {
            if ($table[$t]['name'] == $key) {
               $table[$t]['relations'][$table[$i]['name']] = ['table' => $table[$i]['table'], 'name' => $items['key'], 'key' => $items['name']];
            }
         }
      }
   }
}
//set sql_attribute
$table = mysqltable::addattribute($table);
// print_r($table);
$controller_route = [];
foreach ($table as $item) {
   isset($json_set['table'][$item['name']]) ? '' : $json_set['table'][$item['name']] = false;
}
//deno
if (in_array('deno', $json_set['back-end'])) {
   denoset($table);
}
//php
if (in_array('php', $json_set['back-end'])) {
   phpset($table, $json_set);
}

if (in_array('angular', $json_set['front-end'])) {
   angularset($table, $json_set);
}
foreach ($table as $item) {
   //    if (in_array('php', $json_set['back-end'])) {
   //       //php
   //       $model = fopen_dir(__DIR__ . "/" . $output_path . ucfirst('model/') . ucfirst($item['name']) . '.php');
   //       $model_write = model($item);
   //       fwrite($model, $model_write);
   //       $controller_write = controller($item);
   //       if ($item['controller'] == '') {
   //          $controller = fopen_dir(__DIR__ . "/" . $output_path . ucfirst('controller/') . ucfirst($item['name']) . 'Controller.php');
   //          fwrite($controller, $controller_write);
   //       }
   //       $controller_route[] = ucfirst($item['name']) . 'Controller';
   //       $route_file = fopen_dir(__DIR__ . "/" . $output_path . '../Route/Api/Routes_crud/' . ucfirst($item['name']) . '.php');
   //       $router_model = crud($item['name'], $item['roles'], $item['crud']);
   //       fwrite($route_file, php_wrapper("use App\Controller\{ " . ucfirst($item['name']) . "Controller};" . $router_model));
   //    } elseif (in_array('deno', $json_set['back-end'])) {
   //       //deno
   //       $model = fopen_dir("deno/App/" . ucfirst('model/') . ucfirst($item['name']) . '.ts');
   //       $model_write = denoModel($item);
   //       fwrite($model, $model_write);
   //       $controller_write = denoController($item);
   //       if ($item['controller'] == '') {
   //          $controller = fopen_dir("deno/App/" . ucfirst('controller/') . ucfirst('model/') . ucfirst($item['name']) . 'Controller.ts');
   //          fwrite($controller, $controller_write);
   //       }
   //       //      $controller_route[] = ucfirst($item['name']) . 'Controller';
   //       //      $route_file = fopen_dir(__DIR__."/".$output_path . '../Route/Api/Routes_crud/' . ucfirst($item['name']) . '.ts');
   //       //      $router_model = crud($item['name'], $item['roles'], $item['crud']);
   //       //      fwrite($route_file, php_wrapper("use App\Controller\{ " . ucfirst($item['name']) . "Controller};" . $router_model));
   //    }
   $mysql_write = mysqltable::table($item);
   $mysql_relation = mysqltable::migrate_table($item);
   $mysql = fopen_dir(__DIR__ . "/../database/" . ucfirst('mysql/') . ucfirst($item['name']) . '.sql');
   $mysql_relation_file = fopen_dir(__DIR__ . "/../database/" . ucfirst('mysql/') . ucfirst('relations/') . ucfirst($item['name']) . '_relation.sql');
   fwrite($mysql_relation_file, $mysql_relation);
   fwrite($mysql, $mysql_write);
   //demo vue with cdn
   // $vuedjs = '../vuejs/src/shared/';
   // $Interface = fopen_dir(__DIR__."/".$vuedjs . 'Interface/' . ucfirst('model/') . ucfirst($item['name']) . '.ts');
   // $Interface_write = Interface_set($item);
   // fwrite($Interface, $Interface_write);
   // $vuestore = fopen_dir(__DIR__."/".$vuedjs . 'Store/' . ucfirst('model/') . ucfirst($item['name']) . '.js');
   // $vuestore_write = Vue_StoreJs($item);
   // fwrite($vuestore, $vuestore_write);
   // $vueservice = fopen_dir(__DIR__."/".$vuedjs . 'Service/' . ucfirst('model/') . ucfirst($item['name']) . '.js');
   // $vueservice_write = Vue_ServiceJs($item);
   // fwrite($vueservice, $vueservice_write);
   //
   if ($json_set['table'][$item['name']] == false || $json_set['fresh'] == true) {
      $Interface_write = Interface_set($item);
      if (in_array('vuets', $json_set['front-end'])) {
         $vuedjs = '../vuets/src/shared/';
         $Interface = fopen_dir(__DIR__ . "/" . $vuedjs . 'Interface/' . ucfirst('model/') . ucfirst($item['name']) . '.ts');
         fwrite($Interface, $Interface_write);
         $vuestore = fopen_dir(__DIR__ . "/" . $vuedjs . 'Store/' . ucfirst('model/') . ucfirst($item['name']) . '.js');
         $vuestore_write = Vue_StoreJs($item);
         fwrite($vuestore, $vuestore_write);
         $vueservice = fopen_dir(__DIR__ . "/" . $vuedjs . 'Service/' . ucfirst('model/') . ucfirst($item['name']) . '.js');
         $vueservice_write = Vue_ServiceJs($item);
         fwrite($vueservice, $vueservice_write);
      }
      if (in_array('soildjs', $json_set['front-end'])) {
         $solidjs = '../solidjs/src/shared/';
         $Interface = fopen_dir(__DIR__ . "/" . $solidjs . 'Interface/' . ucfirst('model/') . ucfirst($item['name']) . '.ts');
         fwrite($Interface, $Interface_write);
         $solidstore = fopen_dir(__DIR__ . "/" . $solidjs . 'Store/' . ucfirst('model/') . ucfirst($item['name']) . '.ts');
         $solidstore_write = SolidTsStore($item);
         fwrite($solidstore, $solidstore_write);
         $solidservice = fopen_dir(__DIR__ . "/" . $solidjs . 'Service/' . ucfirst('model/') . ucfirst($item['name']) . '.ts');
         $solidservice_write = SolidServicesTs($item);
         fwrite($solidservice, $solidservice_write);
      }
   }
}
foreach ($route_use_array as $key => $value) {
   $route_use_multiple .= "use $key{" . implode(',', array_unique($value)) . "}; ";
}
$migration_sql = '';
$migration_relation = '';
foreach ($table as $item) {
   $migration_sql .= file_get_contents(__DIR__ . "/../database/" . ucfirst('mysql/') . ucfirst($item['name']) . '.sql', 'TRUE');
   $migration_relation .= file_get_contents(__DIR__ . "/../database/" . ucfirst('mysql/') . ucfirst('relations/') . ucfirst($item['name']) . '_relation.sql', 'TRUE');
}
$migration_sql .= 'INSERT INTO roles (name) VALUES ("' . implode('"),("', array_values(array_unique($roles))) . '");';
file_put_contents(__DIR__ . '/../database/Migration.sql', ($migration_sql . ' ' . $migration_relation));
file_put_contents(__DIR__ . '/../config.json', json_encode($json_set, JSON_PRETTY_PRINT));
