<?php

// Define the path to the Laravel bootstrap file
require __DIR__ . '/../bootstrap/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Create the Kernel instance
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run the schedule:run command
$kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);
