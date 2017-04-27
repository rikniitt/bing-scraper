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

// Path resolver helper
function toRealpath($file) {
    $firstChar = substr($file, 0, 1);
    if ($firstChar === '.' || $firstChar !== '/') {
        return realpath(PROJECT_DIR . '/' . $file);
    } else {
        return realpath($file);
    }
}


// Setup Pimple container
$container = new Pimple\Container();
$container['config'] = $config;


// Setup Monolog
$logDir = toRealpath($config['LOG_FOLDER']);
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
$databasePathInfo = pathinfo($config['DATABASE']);
$databaseDir = toRealpath($databasePathInfo['dirname']);
if (!$databaseDir) {
    throw new Exception('Folder ' . $databasePathInfo['dirname'] . ' must exist to store database!');
}
$container['db.file'] = $databaseDir . '/' . $databasePathInfo['basename'];
$database = new Illuminate\Database\Capsule\Manager();
$database->addConnection([
    'driver' => 'sqlite',
    'database' => $container['db.file']
]);
$database->bootEloquent();
$container['db'] = $database;


// Done. Return pimple container.
$container['logger']->debug('Bootstrapped.');
return $container;
