<?php
include 'Make.php';
include 'Convert.php';
include 'Soap/Soap.php';
include 'Common/Tools.php';
include 'Tools.php';
include 'Exception/DocumentsException.php';
include 'Factories/Parser.php';
include '../../sped-common/src/DOMImproved.php';
include '../../sped-common/src/Strings.php';

$txt = realpath(__DIR__ . "/../storage/TxT.txt");

$textoTeste = file_get_contents($txt);

$obj = new NFePHP\NFSe\DSF\Convert;

$xml = $obj->toXml($textoTeste);
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// $conf = [
//     "tpAmb" => '2',
//     "versao" => '3.0.1',
//     "user" => '02949160000379',
//     "password" => '123456',
// ];

// $conf = json_encode($conf);

// $xml = $xml[0];

// $obj = new NFePHP\NFSe\DSF\Tools($conf);

// $xml = $obj->enviaRPS($xml);
// var_dump($xml);
