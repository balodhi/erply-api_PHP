<?php
session_start();
require './vendor/autoload.php';

include("eapi.php");

// Initialise class
$api = new eapi();

// Configuration settings
$api->clientCode = '500238';
$api->username = 'balodhi@gmail.com';
$api->password = 'testpassword123!@#';
$api->url = "https://".$api->clientCode.".erply.com/api/";

function searchProduct($handle,$productName)
{
        $params = array('findBestMatch'=>1 ,
                    'name'=> $productName
                );
        $result = $handle->sendRequest('getProducts',$params);
        $results = json_decode($result);
    return $results;
}
function deleteProduct($handle,$productID)
{
        $params = array('productID'=>$productID 
                    
                );
        $result = $handle->sendRequest('deleteProduct',$params);
        $results = json_decode($result);
        print_r($results);
        if ($results->status->responseStatus=='ok')
        {
            print("product successfully deleted");
        }
        else
        {
            print("There is some problem in deleting the product");
        }
}
function addProduct($handle,$productName)
{
        $params = array('groupID'=>1 ,
                    'name'=>$productName
                );
        $result = $handle->sendRequest('saveProduct',$params);
        $results = json_decode($result);
        if ($results->status->responseStatus=='ok')
        {
            $res = searchProduct($handle,$productName);
            $value = $res->records;
            print('Product '. $productName . ' Successfull added. with id = '. $value[0]->productID);
        }
        else
        {
            print("There is some problem in adding the product");
        }
        return $res;
}
function datahandler($productid)
{
	global $api;
	$res = searchProduct($api,$productid);
	#print_r($res);
	$value = $res->records;
	#print_r($value[0]->productID);
	#print(implode(', ', $value));
	if(empty($value))
	{
		print('No matching product is found');
		$result = addProduct($api,$productid);
	}
	else
	{
		$pid = $value[0]->productID;
		#print("some value in variable");
		print('record already exist so skipping it'.$pid);
	//	deleteProduct($api,$pid);
	}
}
// Get client groups from API
// No input parameters are needed
#$result = $api->sendRequest("getClientGroups", array());
//$result = searchProduct($api,'1');
//print_r($result);
#print( $result->records[0]->productID);
//$value = $result->records;
#print_r($value[0]->productID);
#print(implode(', ', $value));
//if(empty($value))
//{
//	print('variable is empty');
//	$result = addProduct($api,'1');
//}
//else
//{
//	$pid = $value[0]->productID;
	#print("some value in variable");
//	print('record already exist '.$pid);
//	deleteProduct($api,$pid);
//}

use Kafka\Consumer;
use Kafka\ConsumerConfig;
use Monolog\Handler\StdoutHandler;
use Monolog\Logger;
// Create the logger
$logger = new Logger('my_logger');
// Now add some handlers
$logger->pushHandler(new StdoutHandler());
$config = ConsumerConfig::getInstance();
$config->setMetadataRefreshIntervalMs(10000);
$config->setMetadataBrokerList('127.0.0.1:9092');
$config->setGroupId('test-consumer-group');
$config->setBrokerVersion('1.1.0');
$config->setTopics(array('my-topic'));
//$config->setOffsetReset('earliest');
// if use ssl connect
//$config->setSslLocalCert('/home/vagrant/code/kafka-php/ca-cert');
//$config->setSslLocalPk('/home/vagrant/code/kafka-php/ca-key');
//$config->setSslEnable(true);
//$config->setSslPassphrase('123456');
//$config->setSslPeerName('nmred');
$consumer = new Consumer();
$consumer->setLogger($logger);
$consumer->start(function ($topic, $part, $message) {
    $data = json_decode($message['message']['value'],true);
    $productid = $data['message'];
    datahandler($productid);
    #print_r($res);

    print("************************************");
});
