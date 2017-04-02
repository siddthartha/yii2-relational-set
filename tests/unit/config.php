<?php
/**
 * Author: Anton Sadovnikoff
 * Email: sadovnikoff@gmail.com
 */

return [
	'id'         => 'testApp',
	'basePath'   => __DIR__,
	'vendorPath' => __DIR__ . '/../../vendor',
	'aliases'    => [
		'@web'     => '/',
		'@webroot' => __DIR__ . '/runtime',
		'@vendor'  => __DIR__ . '/../../vendor',
	],

	'components' => [
		'cache'        => [
			'class' => 'yii\caching\DummyCache',
		],
	]
];