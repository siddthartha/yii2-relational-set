<?php
/**
 * Author: Anton Sadovnikoff
 * Email: sadovnikoff@gmail.com
 */

return [
	'id'         => 'testApp',
	'basePath'   => __DIR__,
	'vendorPath' => __DIR__ . '/../vendor',
	'aliases'    => [
        '@common'  => __DIR__,
		'@web'     => '/',
		'@webroot' => __DIR__ . '/runtime',
		'@vendor'  => __DIR__ . '/../vendor',
	],

	'components' => [
		'cache'        => [
			'class' => 'yii\caching\DummyCache',
		],
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn'  => 'sqlite:' . realpath(__DIR__ . "/data") . "/sqlite.db",
        ],
	]
];