<?php

Autoloader::add_classes(array(
    'Autho\\Acl'             => __DIR__.'/classes/acl.php',
    'Autho\\Auth'            => __DIR__.'/classes/auth.php',
    'Autho\\Controller'      => __DIR__.'/classes/controller.php',
    'Autho\\Provider_Normal' => __DIR__.'/classes/provider/normal.php',
    
    'Autho\\Driver'          => __DIR__.'/classes/driver.php',
    'Autho\\Driver_User'     => __DIR__.'/classes/driver/user.php',
    
    'Autho\\Strategy'        => __DIR__.'/classes/strategy.php',
    'Autho\\Strategy_Normal' => __DIR__.'/classes/strategy/normal.php',
    'Autho\\Strategy_OAuth'  => __DIR__.'/classes/strategy/oauth.php',
    'Autho\\Strategy_OAuth2' => __DIR__.'/classes/strategy/oauth2.php',
));