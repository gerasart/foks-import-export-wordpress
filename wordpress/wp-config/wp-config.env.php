<?php
$project = getenv('PROJECT_NAME');

return $env = [
	'production' => [
		'domain' => ["{$project}.com", "www.{$project}.com"],
		'path'   => '',
		'ssl'    => 1,
		'define' => [
			'DB_NAME'     => getenv('DB_NAME'),
			'DB_USER'     => getenv('DB_USER'),
			'DB_PASSWORD' => getenv('DB_PASS'),
			'DB_HOST'     => getenv('DB_HOST'),
			'WP_DEBUG'    => 1,
		],
	],
	'staging'    => [
        'domain' => ["{$project}.lo0.me", "www.{$project}.lo0.me"],
		'path'   => '',
		'ssl'    => 0,
		'define' => [
			'DB_NAME'     => getenv('DB_NAME'),
			'DB_USER'     => getenv('DB_USER'),
			'DB_PASSWORD' => getenv('DB_PASS'),
			'DB_HOST'     => getenv('DB_HOST'),
			'WP_DEBUG'    => 1,
		],
	],
	'development' => [
		'domain'  => 'wp.docker.localhost:8000',
		'path'    => '',
		'ssl'     => 0,
		'define'  => [
			'DB_NAME'     => getenv('DB_NAME'),
			'DB_USER'     => getenv('DB_USER'),
			'DB_PASSWORD' => getenv('DB_PASS'),
			'DB_HOST'     => getenv('DB_HOST'),
			'WP_DEBUG'    => 1,
		],
	],
];
