<?php

return array(
    'class' => '\\Cc\\Mvc\\Auth',
    'SessionName' => 'Pollera-Session',
    'SessionCookie' =>
    array(
        'path' => '/polleras/api/',
        'cahe' => 'nocache,private',
        'time' => '+1 day',
        'dominio' => NULL,
        'httponly' => false,
        'ReadAndClose' => true,
    ),
);
