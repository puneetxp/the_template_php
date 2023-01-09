<?php

use The\{Auth};

$route?->get('login', [Auth::class, 'status']);
$route?->post('register', [Auth::class, 'register']);
$route?->post('login', [Auth::class, 'login']);
$route?->get('logout', [Auth::class, 'logout']);
