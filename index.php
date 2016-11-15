<?php
require_once 'vendor/autoload.php';
require_once 'stopwatch.php';
 
// connect to database
$mysqli = new mysqli('database_host', 'database_user', 'database_password', 'database_name');
if (!empty($mysqli->connect_errno)) {
    throw new \Exception($mysqli->connect_error, $mysqli->connect_errno);
}
 
// create a bot
$bot = new \TelegramBot\Api\Client('bot_token', 'botanio_token');
// run, bot, run!
$bot->run();

$bot->command('start', function ($message) use ($bot) {
    $answer = 'Howdy! Welcome to the stopwatch. Use bot commands or keyboard to control your time.';
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

$bot->command('go', function ($message) use ($bot, $mysqli) {
    $stopwatch = new Stopwatch($mysqli, $message->getChat()->getId());
    $stopwatch->start();
    $bot->sendMessage($message->getChat()->getId(), 'Stopwatch started. Go!');
});

$bot->command('status', function ($message) use ($bot, $mysqli) {
    $stopwatch = new Stopwatch($mysqli, $message->getChat()->getId());
    $answer = $stopwatch->status();
    if (empty($answer)) {
        $answer = 'Timer is not started.';
    }
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

$bot->command('stop', function ($message) use ($bot, $mysqli) {
    $stopwatch = new Stopwatch($mysqli, $message->getChat()->getId());
    $answer = $stopwatch->status();
    if (!empty($answer)) {
        $answer = 'Your time is ' . $answer . PHP_EOL;
    }
    $stopwatch->stop();
    $bot->sendMessage($message->getChat()->getId(), $answer . 'Stopwatch stopped. Enjoy your time!');
});
?>
