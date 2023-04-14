<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

// Charger les variables d'environnement à partir du fichier .env
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env.local');

$input = new ArgvInput(['bin/console', 'app:send-reminder-emails']);
$kernel = new \App\Kernel('prod', false);
$application = new Application($kernel);
$application->run($input);
