#!/usr/bin/ea-php74
<?php
 chdir(__DIR__);

// Include Laravel's autoload file
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';

// Run the scheduler
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\StringInput('schedule:run'),
    $output = new Symfony\Component\Console\Output\BufferedOutput
);

// Output the result
echo $output->fetch();

// Exit with the status returned by the scheduler
exit($status);