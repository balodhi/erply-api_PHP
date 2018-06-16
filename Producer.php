<?php
require './vendor/autoload.php';
date_default_timezone_set('PRC');

#$hosts = 'localhost:2181';
$config = \Kafka\ProducerConfig::getInstance();
$config->setMetadataRefreshIntervalMs(10000);
$config->setMetadataBrokerList('127.0.0.1:9092');
$config->setBrokerVersion('1.1.0');
#$config->setRequiredAck(1);
$config->setIsAsyn(false);
$config->setProduceInterval(500);
$producer = new \Kafka\Producer(function() {
     $msg = json_encode([
         "message"=> 'bilal'
     ]);
     return array(
         array(
             'topic' => 'my-topic',
             'value' => $msg,
             'key' => 'testkey',
         ),
     );
 });
$producer->success(function($result) {
	print("fine");
	var_dump($result);
});
$producer->error(function($errorCode) {
		print("error");
		var_dump($errorCode);
});
$producer->send(true);
print("end kafka send!");
