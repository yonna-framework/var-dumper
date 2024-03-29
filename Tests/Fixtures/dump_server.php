<?php

use Yonna\VarDumper\Cloner\Data;
use Yonna\VarDumper\Cloner\VarCloner;
use Yonna\VarDumper\Dumper\CliDumper;
use Yonna\VarDumper\Server\DumpServer;
use Yonna\VarDumper\VarDumper;

$componentRoot = $_SERVER['COMPONENT_ROOT'];

if (!is_file($file = $componentRoot.'/vendor/autoload.php')) {
    $file = $componentRoot.'/../../../../vendor/autoload.php';
}

require $file;

$cloner = new VarCloner();
$cloner->setMaxItems(-1);

$dumper = new CliDumper(null, null, CliDumper::DUMP_LIGHT_ARRAY | CliDumper::DUMP_STRING_LENGTH);
$dumper->setColors(false);

VarDumper::setHandler(function ($var) use ($cloner, $dumper) {
    $data = $cloner->cloneVar($var)->withRefHandles(false);
    $dumper->dump($data);
});

$server = new DumpServer(getenv('VAR_DUMPER_SERVER'));

$server->start();

echo "READY\n";

$server->listen(function (Data $data, array $context, $clientId) {
    dump((string) $data, $context, $clientId);

    exit(0);
});
