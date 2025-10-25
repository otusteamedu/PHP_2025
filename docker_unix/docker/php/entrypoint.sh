#!/bin/sh

set -e

mkdir -p /var/run/php
chown www-data:www-data /var/run/php

exec php-fpm
