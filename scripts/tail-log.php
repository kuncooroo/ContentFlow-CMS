<?php

declare(strict_types=1);

$logFile = dirname(__DIR__).'/storage/logs/laravel.log';

if (! is_dir(dirname($logFile))) {
    mkdir(dirname($logFile), 0755, true);
}

if (! is_file($logFile)) {
    touch($logFile);
}

echo "Tailing {$logFile} (Pail unavailable — using file tail fallback).\n";

$offset = filesize($logFile) ?: 0;

while (true) {
    clearstatcache(true, $logFile);

    if (! is_file($logFile)) {
        usleep(250_000);

        continue;
    }

    $size = filesize($logFile) ?: 0;

    if ($size > $offset) {
        $handle = fopen($logFile, 'rb');

        if ($handle !== false) {
            fseek($handle, $offset);
            $chunk = stream_get_contents($handle) ?: '';
            fclose($handle);

            if ($chunk !== '') {
                echo $chunk;
            }

            $offset = $size;
        }
    }

    usleep(250_000);
}
