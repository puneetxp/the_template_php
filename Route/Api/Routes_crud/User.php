<?php use App\Controller\{ UserController};$route?->crud(['c','r','u'], 'user',[ 'read'=>['admin'],'write'=>['admin'],'update'=>['admin'],'delete'=>['-']] , UserController::class);
?> 