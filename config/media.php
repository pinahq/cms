<?php

return [
    'default' => 'local',
    'local' => [
        'driver' => 'local',
        'root' =>  __DIR__ . '/../public/uploads',
        'url' => '/uploads',
        'resize' => true,
    ],
];