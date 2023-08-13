<?php

use App\Controller\Web\authController;

$login = [
 "islogin" => true,
 "child" => [[
  "path" => "dashboard",
  "handler" => [authController::class, "dashboard"]
 ]]
];
