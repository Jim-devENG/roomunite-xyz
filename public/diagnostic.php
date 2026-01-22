<?php
header('Content-Type: text/plain');
echo 'PHP Version: ' . PHP_VERSION . PHP_EOL;
echo 'SAPI: ' . php_sapi_name() . PHP_EOL;
echo 'Document Root: ' . (\['DOCUMENT_ROOT'] ?? 'Unknown') . PHP_EOL;
echo 'Error Log: ' . ini_get('error_log') . PHP_EOL;
