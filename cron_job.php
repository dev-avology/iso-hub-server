<?php

// Load Composer's autoload file
// require __DIR__ . '/../vendor/autoload.php';
require __DIR__.'/../vendor/autoload.php';

// Load the Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Create the Kernel instance
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run the schedule:run command
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

// Exit with the appropriate status code
exit($status);
