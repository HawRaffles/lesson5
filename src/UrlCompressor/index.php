<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Carbon\Carbon;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Study\UrlCompressor\Helpers\CheckUrl;
use Study\UrlCompressor\Actions\ConvertUrl;

$config = [
    'timeout' => 6,
    'responses' => [
        200 => true,
        301 => true,
        302 => true,
        404 => true
    ],
    'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (HTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36',
    'datafile' => __DIR__ . '/data.json',
    'logfile' => [
        'error' => __DIR__ . '/../log/error.log',
        'warning' => __DIR__ . '/../log/warning.log',
        'info' => __DIR__ . '/../log/info.log',
    ]
];

$logger = new Logger('general');
$logger->pushHandler(new StreamHandler($config['logfile']['error'], level::Error));
$logger->pushHandler(new StreamHandler($config['logfile']['warning'], level::Warning));
$logger->pushHandler(new StreamHandler($config['logfile']['info'], level::Info));

$carbon = new Carbon(new DateTime());

do {
    $inputType = readline('Оберіть тип завдання (1 - скоротити URL;  2 - відновити URL; 0 - вихід): ');
} while (!in_array($inputType, [0,1,2]));

try {
    $urlData = new ConvertUrl($config['datafile'], $carbon->addYear(), $logger);
    switch ($inputType) {
        case 0:
            exit();
        case 1:
            do {
                $validUrl = new CheckUrl($config['timeout'], $config['responses'], $config['ua']);
            } while (!$validUrl->urlType);
            echo 'Ваш скорочений URL: ' . $urlData->encode($validUrl->checkedUrl);
            break;
        case 2:
            $inputCode = readline('Введіть закодований URL: ');
            echo 'Ваш відновлений URL: ' . $urlData->decode($inputCode);
            break;
    }
} catch (Exception $e) {
    echo $e->getMessage();
} catch (Error $error) {
    echo $error->getMessage();
}

echo PHP_EOL;
