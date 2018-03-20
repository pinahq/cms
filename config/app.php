<?php

return array(
    'host' => 'pinacms.local',
    'scheme' => 'http',
    #'template' => '',
    'version' => '1',
    'path' => realpath(__DIR__.'/../app'),
    'charset' => 'utf-8',
    'timezone' => 'Europe/Moscow',
    'uploads' => __DIR__.'/../public/uploads',
    'tmp' => __DIR__.'/../var/temp',
    'templater' => array(
        'cache' => __DIR__.'/../var/cache',
        'compiled' => __DIR__.'/../var/compiled',
    ),
    'cronLockFile' => '/tmp/pina-lock.txt',
    'sharedDepencies' => [
        \Pina\EventQueueInterface::class => \Pina\CronEventQueue::class
    ],
);