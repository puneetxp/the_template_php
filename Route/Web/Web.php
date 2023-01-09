<?php

use The\{Route};
use App\Controller\{PageController};
$route?->get('/', [PageController::class, 'Home']);
$route?->get('/contact-us', [PageController::class, 'Contact_Us']);
$route?->get('/about-us', [PageController::class, 'About_Us']);
$route?->get('/privacy-policy', [PageController::class, 'Privacy_Policy']);
