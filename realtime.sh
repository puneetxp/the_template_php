#!/bin/sh

php ./setup/compile/php.php
php ./php/set.php
while inotifywait -e modify -r ./Resource/View/; do
    php ./setup/compile/php.php
done &
while inotifywait -e modify -r ./php/Routes/pre/; do
    php ./php/set.php
done &
sass --style=compressed  --watch Resource/Style:public/assets/css &
composer dump-autoload --working-dir=php
