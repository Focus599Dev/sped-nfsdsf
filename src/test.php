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

// $textoTeste = file_get_contents($txt);

$textoTeste = 'NOTAFISCAL|1
A|0000001901|3.0.1||01|2020
B|02949160000379|59027|1
C|02949160000379|59027
E|PAULO DE ARAUJO RODRIGUES E OUTROS |FAZENDA SANTA izabel||1634565400|paulo@paulo.com.br|SN|SEM COMPLE|CENTRO|3524303|SP|14870970|RUA
E02|08006300000190||391117051115|PAULO DE ARAUJO RODRIGUES E OUTROS|S|F|SP|3524303||1
F|||
H|000017
H01|000017|E|1
M|600.00|0|3.90|18.00||0.00|6.00|2|24.00||0|600.00|4.000000000|572.10|0|0||0.00|0.00| TESTE XXXXXXXXXXXX||
N|1401|4520001||SERVIÇO OFICINA|3524303||||||000010|300.000000|2.000|0.00
N01||||||
W|2020-01-16T17:07:56|1|3|2|2|1';

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
