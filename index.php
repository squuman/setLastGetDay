<?php
require __DIR__ .'/vendor/autoload.php';

$client = new \RetailCrm\ApiClient(
    'url',
    'key',
    \RetailCrm\ApiClient::V5
);

logger('request.log',$_REQUEST);
$_GET['id'] = 206881;

if (!isset($_GET['id']))
    die('Nein');

$order = $client->request->ordersGet($_GET['id'],'id');

$date = new DateTime($order['order']['createdAt']);
$day = $date->format('D');
$deliveryDay = '';

if ($day == 'Mon')
    $date->modify('+ 4 days');
elseif($day == 'Wed')
    $date->modify('+ 5 days');
elseif($day == 'Fri')
    $date->modify('+ 5 days');

$orderEdit = $client->request->ordersEdit([
    'id' => $order['order']['id'],
    'customFields' => [
        'last_date_samovivoz' => $date->format('Y-m-d')
    ]
],'id',$order['order']['site']);

logger('orderEdit.log',[
    'date' => date('Y-m-d H:i:s'),
    'data' => print_r($orderEdit,true)
]);

/*
 *

 */

function logger($filename = 'noname.log',$data = array()) {
    if (!is_dir(__DIR__ .'/logs'))
        mkdir(__DIR__ .'/logs');
    $fd = fopen(__DIR__ .'/logs/' . $filename,'a');
    fwrite($fd,print_r($data,true) . "\n");
    fclose($fd);
}
