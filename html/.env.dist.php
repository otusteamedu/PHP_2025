<?php

putenv('STORE=memcached'); // memcached | redis (default)

putenv('REDIS_HOST=redis');
putenv('REDIS_PORT=6379');

putenv('MEMCACHED_HOST=memcached');
putenv('MEMCACHED_PORT=11211');
