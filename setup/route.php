<?php
function folderscan($dir)
{
 $pattern_route = '/\$route.*?;/';
 $pattern_use_only = '/use.*?\w;/';
 $pattern_use_multple = "/use (.*?){(.*?)};/";
 $route_use_single = '';
 $route_use_array = [];
 $route_use_array['The\\'] = ["Route"];
 $route_app = ' (new Route())';
 $d = scandir($dir);
 for ($i = 2; $i < count($d); $i++) {
  if (is_file("$dir/$d[$i]")) {
   if (preg_match_all("/(.*).php/", $d[$i])) {
    $router_mode_raw = str_replace("{ ", "{", str_replace(["<?php", "?>", "\n", "\r\n", "\r", "\t", "    "], "", file_get_contents("$dir/$d[$i]", 'TRUE')));
    if ($router_mode_raw != '') {
     preg_match_all($pattern_use_only, $router_mode_raw, $use_temp_single);
     if ($use_temp_single[0] != []) {
      foreach ($use_temp_single as $item) {
       $route_use_single .= $item;
      }
     }
     preg_match_all($pattern_use_multple, $router_mode_raw, $use_temp_multiple, PREG_SET_ORDER);
     if ($use_temp_multiple[0] != []) {
      foreach ($use_temp_multiple as $item) {
       if (isset($route_use_array[$item[1]])) {
        foreach (explode(',', $item[2]) as $controller_roter) {
         $route_use_array[$item[1]][] = $controller_roter;
        }
       } else {
        $route_use_array[$item[1]] = array_values(explode(',', $item[2]));
       }
      }
     }
     preg_match_all($pattern_route, $router_mode_raw, $router_temp);
     if ($router_temp[0] != []) {
      foreach ($router_temp[0] as $item) {
       $route_app .= "\n" . preg_replace('/(;(?!.*;))/', '', $item);
      }
     }
    }
   }
  } elseif (is_dir("$dir/$d[$i]")) {
   $forlderscan = folderscan("$dir/$d[$i]");
   $route_use_single .= $forlderscan['route_use_single'];
   $route_app .= $forlderscan['route_app'];
   foreach ($forlderscan['route_use_array'] as $key => $value) {
    if (isset($route_use_array[$key])) {
     foreach ($value as $item) {
      $route_use_array[$key][] = $item;
     }
    } else {
     $route_use_array[$key] = $value;
    }
   }
  }
 }
 return [
  "route_use_single" => $route_use_single,
  "route_use_array" => $route_use_array,
  "route_app" => $route_app,
 ];
}
function route_php_compile(string $dir, string $output)
{
 $route_dir = folderscan($dir);
 $route_use_multiple = '';
 foreach ($route_dir['route_use_array'] as $key => $value) {
  $route_use_multiple .= "use $key{" . implode(',', array_unique($value)) . "}; ";
 }
 $route = fopen_dir($output);
 fwrite($route, str_replace('$route', '', php_w($route_dir['route_use_single'] . $route_use_multiple . $route_dir['route_app'] . "\n?->not_found();")));
}
