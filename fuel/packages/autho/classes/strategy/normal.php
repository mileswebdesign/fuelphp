<?php

namespace Autho;

class Strategy_Normal extends Strategy {

    public $provider = null;

    public function __construct($provider)
    {
        parent::__construct($provider);

        $this->provider = Provider_Normal::factory();

        return $this;
    }
    
    public function authenticate()
    {
        // get user data from cookie
        $users              = \Cookie::get('_users');

        // user data shouldn't be null if there user authentication available, if not populate from default
        if (!is_null($users)) 
        {
            $users          = unserialize(\Crypt::decode($users));
        }
        else
        {
            $users          = new \stdClass();
            $users->id      = 0;
            $users->_hash   = '';
        }

        $this->provider->access_token((array) $users);

        return $this;
    }

}