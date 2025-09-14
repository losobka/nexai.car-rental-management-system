<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

foreach ([
    'cache:clear --no-warmup',
//    'doctrine:database:drop --if-exists --force',
//    'doctrine:database:create',
//    'doctrine:schema:create',
//    'doctrine:fixtures:load --no-interaction --append'
] as $command) {
    ob_start();

    passthru(sprintf(
        'APP_ENV=%s php "%s/../bin/console" %s 2>&1',
        $_ENV['APP_ENV'],
        __DIR__,
        $command
    ));

    $output = ob_get_contents();

    ob_end_clean();
}

Zenstruck\Foundry\Test\UnitTestConfig::configure(
//    instantiator: Zenstruck\Foundry\Object\Instantiator::withConstructor()
//        ->allowExtra()
//        ->alwaysForce(),
    faker: Faker\Factory::create('pl_PL')
);