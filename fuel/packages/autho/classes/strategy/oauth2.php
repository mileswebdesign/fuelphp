<?php

namespace Autho;

class Strategy_OAuth2 extends Strategy {
    
    public $provider;
    
    public function authenticate()
    {
        // Load the provider
        $provider = \OAuth2\Provider::factory($this->provider, $this->config);
        
        $provider->authorize(array(
            'redirect_uri' => \Uri::create(\Config::get('autho.urls.callback', \Request::active()->route->segments[0].'/callback').'/'.$this->provider)
        ));
    }
    
    public function callback()
    {
        // Load the provider
        $this->provider = \OAuth2\Provider::factory($this->provider, $this->config);
        
        try
        {
            $params = $this->provider->access(\Input::get('code'));
            
            return (object) array(
                'token' => $params['access_token'],
                'secret' => null,
            );
        }
    
        catch (Exception $e)
        {
            exit('That didnt work: '.$e);
        }
    }
    
}