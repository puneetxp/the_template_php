<?php use App\Controller\{ RoleController};$route?->crud(['c','r','u','d'], 'role',[ 'read'=>['admin'],'write'=>['admin'],'update'=>['admin'],'delete'=>['admin']] , RoleController::class);
?> 