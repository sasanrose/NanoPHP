<?php
$config = array(
    'default_controller' => 'Welcome',
    'default_action'     => 'Index',
    'production'         => true,
    'default_layout'     => 'layout',
    'timezone'           => 'Asia/Tehran',
    'log' => array(
        'driver'    => 'file',
        'threshold' => 3, /* 0: Disable Logging 1: Error 2: Notice 3: Info 4: Warning 5: Debug */
        'file'      => array(
            'directory' => 'logs'
        )
    ),
    'database'  => array(
        'driver' => 'redis',
        'mysql'  => array(
            'host'     => 'localhost',
            'username' => 'root',
            'password' => 'root'
        ),
        'redis' => array(
            'host'     => 'localhost',
            'port'     => '6379',
            'password' => null,
            'database' => 0
        )
    ),
    'session' => array(
        'lifetime'       => 7200,
        'gc_probability' => 2,
        'name'           => 'nanophpsession'
    )
);

return $config;
