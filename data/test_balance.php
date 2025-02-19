<?php
header("Content-Type: text/plain");
echo "Request Chain: " . ($_SERVER['HTTP_X_REQUEST_CHAIN'] ?? 'Unknown') . gethostname(). "\n";