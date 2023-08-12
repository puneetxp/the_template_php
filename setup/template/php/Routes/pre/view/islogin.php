<?php

use App\Controller\Web\authController;

$login = [
 [
  "ilogin" => true,
  "child" => [
   "path" => "dashboard",
   "handler" => [authController::class, "dashboard"]
  ]
 ]
];
