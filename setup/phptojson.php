<?php

foreach (glob("model/*.php") as $filename) {
  require_once $filename;
  $json = str_replace('php', 'json', $filename);
  fwrite(fopen($json, 'w'), json_encode($y, JSON_PRETTY_PRINT));
}