<?php

$y = fopen(__DIR__.'/../Compiled/Component.php', 'w');
$x = "<?php namespace App\Compiled; \n class Component {";
$dir = __DIR__ . '/../../Resources';
function folderscan($dir)
{
    $x = '';

    foreach (scandir($dir) as $file) {
        if ($file == '.') {
        } elseif ($file == "..") {
        } elseif (is_file("$dir/$file")) {
            $x .= ComponentDir("$dir/$file");
        } elseif (is_dir("$dir/$file")) {
            $x .= folderscan("$dir/$file");
        }
    }
    return $x;
}


function ComponentDir($filename)
{
    $filename;
    $pattern = "/<\?php(.*)\?>/";
    $small = str_replace(["\n", "\r\n", "\r", "\t", "    ","   ", "                  "], "", fread(fopen($filename, "r"), filesize($filename)));
    preg_match_all($pattern, $small, $use_temp_multiple, PREG_SET_ORDER);
    return $use_temp_multiple[0][1];
}

$x .= folderscan($dir);

$x .= " }";
$x = str_replace('?><?php', '', $x);
$x = str_replace('function ', 'public static function ', $x);
fwrite($y, $x);
