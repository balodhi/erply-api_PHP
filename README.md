# Readme

This is an api usage implementation of erply.com with apache-kafka
Consumer receives the product from producer and use erply.com api to add the product if it is not already present.

pacakge reads kafka broker at 'localhost:9092'  
default topic is 'my-topic'

Please create this topic in kafka before running the script.

# Packages Required

use PHP composer

# Running

REPLACE the values in consumer.php  
$api->clientCode = "CLIENT_CODE";  
$api->username = "USER_NAME";  
$api->password = "PASSWORD";  

commandline:  
go to the package directory  
composer update  

php Producer.php  
php Consumer.php  



# Note
1. Apache kafka must be configured and running before running the python script.  
2. kafka version = kafka_2.12-1.1.0  
