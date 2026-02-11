<?php


echo "Hello, Otus!";

echo "Current User ID: " . posix_getuid() . "<br>";
echo "Current Group ID: " . posix_getgid() . "<br>";
echo "User Name: " . posix_getpwuid(posix_getuid())['name'];

phpinfo();

