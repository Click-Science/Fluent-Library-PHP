<?php

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(__DIR__ . '/../library'),
    get_include_path(),
)));

if (count($argv) != 4) {
    echo "Usage: {$argv[0]} <filename> <filetype> <filepath>\n";
    exit;
}

list($script, $name, $type, $path) = $argv;

require_once 'Fluent.php';
require_once 'Fluent/Content.php';
require_once 'Fluent/Message.php';
require_once 'Fluent/Transport.php';
require_once 'Fluent/Transport/Remote.php';

Fluent::$defaults = array(
    'key'      => '9fe630283b5a62833b04023c20e43915',
    'secret'   => 'test',
    'sender'   => array('name' => 'ACME', 'address' => 'christian@clickscience.co.za'),
);

Fluent\Transport\Remote::$endpoint = 'http://localhost/fluent-web-service/v3';
Fluent\Transport\Remote::$debug = true;

try {
    $messageId = Fluent::message()
        ->setTitle('My little pony')
        ->addParagraph('I love my pony very much.')
        ->addCallout('http://www.mypony.com', 'Like my pony')
        ->setTeaser('This is a teaser')
        ->subject('Testing attachments')
        ->attach($name, $type, file_get_contents($path))
        ->to('christianjburger@gmail.com')
        ->send('remote');
    echo 'Sent message: ' . $messageId . "\n";
} catch (Fluent\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
