<?php

use Puneetxp\CompilePhp\setup;

require "./vendor/autoload.php";
// require_once __DIR__ . '/setup/autosetup.php';
$setup = new setup(__DIR__);
$setup->config();
//(new mysqltable())->migrate();
// (new setup(__DIR__))->table_set()->php_set()->deno_set()->angular_set()->write();