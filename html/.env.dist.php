<?php

declare(strict_types=1);

putenv('DATABASE_DSN=sqlite:/var/www/html/runtime/persistent/database.sqlite');
putenv('DATABASE_USERNAME=');
putenv('DATABASE_PASSWORD=');

putenv('QUEUE_REALTIME_HOST=rabbitmq');
putenv('QUEUE_REALTIME_PORT=5672');
putenv('QUEUE_REALTIME_USER=default');
putenv('QUEUE_REALTIME_PASSWORD=default');
