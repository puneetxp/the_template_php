<?php
require __DIR__ . "/../../vendor/autoload.php";
require __DIR__ . "/Ipublic.php";
require __DIR__ . "/Isuper.php";
require __DIR__ . "/Auth.php";
require __DIR__ . "/view.php";
require __DIR__ . "/env.php";

use The\compile\{RouteCompile, Thefun};

$routes = [
    [
        "path" => "api",
        "child" =>
        [
            $ipublic,
            $isuper,
            ...$auth,
        ]
    ],
    ...$view,
    ...$env,
];

$route = var_export((new RouteCompile($routes))->route, true);
$routx = Thefun::fopen_dir(__DIR__ . "/../web.php");
fwrite($routx, preg_replace(["/'(.*::class)'/"], [' ${1} '], "<?php \n" . '$route =' . stripslashes($route) . ";"));
