<?php
use Ouzo\Utilities\Arrays;
use WSDL\Annotation\SoapBinding;
use WSDL\Builder\Method;
use WSDL\Builder\Parameter;
use WSDL\Builder\WSDLBuilder;
use WSDL\Lexer\Tokenizer;
use WSDL\Parser\Parser;
use WSDL\WSDL;

require_once '../../vendor/autoload.php';

ini_set("soap.wsdl_cache_enabled", 0);

$tokenizer = new Tokenizer();

$tokens = $tokenizer->lex('string $token');
$parser = new Parser($tokens);
$parameters1a = $parser->S();
$tokens = $tokenizer->lex('string $name');
$parser = new Parser($tokens);
$parameters1b = $parser->S();
$tokens = $tokenizer->lex('int $age');
$parser = new Parser($tokens);
$parameters1c = $parser->S();
$tokens = $tokenizer->lex('string[] $count');
$parser = new Parser($tokens);
$parameters1d = $parser->S();
$tokens = $tokenizer->lex('object $user { int $age object $agent { int $code string $name } }');
$parser = new Parser($tokens);
$parameters1e = $parser->S();
$parameters1 = [
    new Parameter(Arrays::firstOrNull($parameters1a), true),
    new Parameter(Arrays::firstOrNull($parameters1b)),
    new Parameter(Arrays::firstOrNull($parameters1c)),
    new Parameter(Arrays::firstOrNull($parameters1d)),
    new Parameter(Arrays::firstOrNull($parameters1e))
];

$tokens = $tokenizer->lex('string $nameWithAge');
$parser = new Parser($tokens);
$return1 = new Parameter(Arrays::firstOrNull($parser->S()));

$tokens = $tokenizer->lex('int $max');
$parser = new Parser($tokens);
$parameters2 = $parser->S();

$tokens = $tokenizer->lex('string[] $count');
$parser = new Parser($tokens);
$return2 = $parser->S();

$tokens = $tokenizer->lex('object $user { int $age }');
$parser = new Parser($tokens);
$parameters3 = $parser->S();

$tokens = $tokenizer->lex('string $message');
$parser = new Parser($tokens);
$return3 = $parser->S();

$builder = WSDLBuilder::instance()
    ->setName('SimpleSoapServer')
    ->setTargetNamespace('http://foo.bar/simplesoapserver')
    ->setNs('http://foo.bar/simplesoapserver/types')
    ->setLocation('http://localhost:7777/wsdl-creator/examples/rpc_literal/new.php')
    ->setStyle(SoapBinding::RPC)
    ->setUse(SoapBinding::LITERAL)
    ->setMethod(new Method('getNameWithAge', $parameters1, $return1));
//    ->setMethod(new Method('countTo', $parameters2, $return2))
//    ->setMethod(new Method('userInfo', $parameters3, $return3));

$wsdl = WSDL::fromBuilder($builder);

if (isset($_GET['wsdl'])) {
    $wsdl->create();
    exit;
}

$server = new SoapServer('http://localhost:7777/wsdl-creator/examples/rpc_literal/new.php?wsdl', array(
    'uri' => 'http://foo.bar/simplesoapserver',
    'location' => $builder->getLocation(),
    'style' => SOAP_RPC,
    'use' => SOAP_LITERAL
));
$server->setClass('NewSimpleSoapServer');
$server->handle();

class NewSimpleSoapServer
{
    public function getNameWithAge($name, $age, $countTo)
    {
        return 'Your name is: ' . $name . ' and you have ' . $age . ' years old [' . implode(', ', $countTo) . ']';
    }
}
