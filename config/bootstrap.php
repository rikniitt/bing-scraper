<?php

define('PROJECT_DIR', realpath(__DIR__ . '/..'));
require_once PROJECT_DIR . '/vendor/autoload.php';


// Parse configuration. Throws if config.file doesn't exist.
$configFile = PROJECT_DIR . '/config/config.file';
if (!is_readable($configFile)) {
    throw new Exception('Configuration file ' . $configFile . ' must be readable!');
}
$parser = new M1\Env\Parser(file_get_contents($configFile));
$config = $parser->getContent();


// Basic php config
date_default_timezone_set($config['TIMEZONE']);


// Setup Pimple container
$container = new Pimple\Container();
$container['config'] = $config;


// Setup Monolog
$logDirSetting = $config['LOG_FOLDER'];
$firstChar = substr($logDirSetting, 0, 1);
if ($firstChar === '.' || $firstChar !== '/') {
    $logDir = realpath(PROJECT_DIR . '/' . $logDirSetting);
} else {
    $logDir = realpath($logDirSetting);
}
if (!is_dir($logDir)) {
    throw new Exception('Log folder ' . $logDir . ' must exist!');
}
$logFile = sprintf('%s/%s_%s.log', $logDir, date('Ym'), $config['LOG_NAME']);
$monolog = new Monolog\Logger($config['LOG_NAME']);
$stream = new Monolog\Handler\StreamHandler($logFile, $config['LOG_LEVEL'], true, 0664);
$monolog->pushHandler($stream);
$format = "[%datetime%] %level_name%: %message% %context% %extra%\n";
$formatter = new Monolog\Formatter\LineFormatter($format, 'Y-m-d H:i:s');
foreach ($monolog->getHandlers() as $handler) {
    $handler->setFormatter($formatter);
}
$container['logger'] = $monolog;


// Setup Illuminate database connection
$database = new Illuminate\Database\Capsule\Manager();
$database->addConnection([
    'driver' => 'sqlite',
    'database' => $config['DATABASE']
]);
$database->bootEloquent();
$container['db'] = $database;


// Done. Return pimple container.
$container['logger']->debug('Bootstrapped.');
return $container;
