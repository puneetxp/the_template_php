<?php

use App\Controller\{Active_roleController};

$route?->crud(['c', 'r', 'u', 'd'], 'active_role', ['read' => ['admin'], 'write' => ['admin'], 'update' => ['admin'], 'delete' => ['admin']], Active_roleController::class);
