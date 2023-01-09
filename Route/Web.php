<?php

use The\{Route};
use App\Controller\{PageController};

(new Route())
 ?->get('/', [PageController::class, 'Home'])
 ?->get('/contact-us', [PageController::class, 'Contact_Us'])
 ?->get('/about-us', [PageController::class, 'About_Us'])
 ?->get('/privacy-policy', [PageController::class, 'Privacy_Policy'])
 ?->not_found();
