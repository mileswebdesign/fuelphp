<?php

namespace Autho;

class Controller extends \Controller {
    
    public function before()
    {
        parent::before();

        // Load the configuration for this provider
        \Config::load('autho', 'autho');
    }

    public function action_session($provider)
    {
        Strategy::factory($provider)->authenticate();
    }

    public function action_callback($provider)
    {
        $strategy = Strategy::factory($provider);
        
        Strategy::login_or_register($strategy);
    }

}