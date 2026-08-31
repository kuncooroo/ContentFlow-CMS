<?php

declare(strict_types=1);

chdir(dirname(__DIR__));

$logCommand = function_exists('pcntl_fork')
    ? 'php artisan pail --timeout=0'
    : 'php scripts/tail-log.php';

$processes = [
    'php artisan serve',
    'php artisan queue:listen --tries=1 --timeout=0',
    $logCommand,
    'npm run dev',
];

$colors = '#93c5fd,#c4b5fd,#fb7185,#fdba74';
$names = 'server,queue,logs,vite';

$quoted = array_map(
    static fn (string $command): string => escapeshellarg($command),
    $processes
);

$command = sprintf(
    'npx concurrently -c %s %s --names=%s --kill-others',
    escapeshellarg($colors),
    implode(' ', $quoted),
    escapeshellarg($names)
);

passthru($command, $exitCode);

exit($exitCode);
