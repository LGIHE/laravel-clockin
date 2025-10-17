<?php

echo "Testing SMTP connections...\n\n";

$hosts = [
    ['host' => 'live.smtp.mailtrap.io', 'port' => 587],
    ['host' => 'live.smtp.mailtrap.io', 'port' => 2525],
    ['host' => 'live.smtp.mailtrap.io', 'port' => 465],
    ['host' => 'mail.lgfug.org', 'port' => 587],
    ['host' => 'mail.lgfug.org', 'port' => 465],
    ['host' => 'mail.lgfug.org', 'port' => 25],
];

foreach ($hosts as $test) {
    $host = $test['host'];
    $port = $test['port'];
    
    echo "Testing {$host}:{$port}... ";
    
    $errno = 0;
    $errstr = '';
    $timeout = 5;
    
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    
    if ($socket) {
        echo "✓ SUCCESS - Port is open!\n";
        fclose($socket);
    } else {
        echo "✗ FAILED - {$errstr} (Error: {$errno})\n";
    }
}

echo "\n✓ Test complete!\n";
