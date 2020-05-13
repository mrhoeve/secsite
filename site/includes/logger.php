<?php
require dirname(__FILE__) . "\Psr\Log\LoggerInterface.php";
require dirname(__FILE__) . "\monolog\ResettableInterface.php";
require dirname(__FILE__) . "\monolog\Handler\HandlerInterface.php";
require dirname(__FILE__) . "\monolog\Handler\Handler.php";
require dirname(__FILE__) . "\monolog\Handler\AbstractHandler.php";
require dirname(__FILE__) . "\monolog\Handler\FormattableHandlerInterface.php";
require dirname(__FILE__) . "\monolog\Handler\ProcessableHandlerInterface.php";
require dirname(__FILE__) . "\monolog\Handler\ProcessableHandlerTrait.php";
require dirname(__FILE__) . "\monolog\Formatter\FormatterInterface.php";
require dirname(__FILE__) . "\monolog\Formatter\NormalizerFormatter.php";
require dirname(__FILE__) . "\monolog\Formatter\LineFormatter.php";
require dirname(__FILE__) . "\monolog\Handler\FormattableHandlerTrait.php";
require dirname(__FILE__) . "\monolog\Handler\AbstractProcessingHandler.php";
require dirname(__FILE__) . "\monolog\Handler\StreamHandler.php";
require dirname(__FILE__) . "\monolog\Logger.php";
require dirname(__FILE__) . "\monolog\DateTimeImmutable.php";
require dirname(__FILE__) . "\monolog\ErrorHandler.php";
require dirname(__FILE__) . "\monolog\Registry.php";
require dirname(__FILE__) . "\monolog\SignalHandler.php";
require dirname(__FILE__) . "\monolog\Utils.php";
require dirname(__FILE__) . "\Psr\Log\LogLevel.php";

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('somesite');
$log->pushHandler(new StreamHandler(__DIR__.'/../logs/somesite.log', Logger::DEBUG));
