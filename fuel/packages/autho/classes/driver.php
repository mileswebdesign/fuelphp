<?php

namespace Autho;

abstract class Driver {

    /**
     * Adapter object
     *
     * @access  protected
     * @var     object
     */
    protected $strategy  = null;
    
    /**
     * Auth data
     *
     * @static
     * @access  protected
     * @var     object|array
     */
    protected $data     = null;

    /**
     * Return TRUE/FALSE whether visitor is logged in to the system
     * 
     * Usage:
     * 
     * <code>false === \Autho\Auth::instance()->is_logged()</code>
     *
     * @access  public
     * @return  bool
     */
    public function is_logged()
    {
        return ($this->data['id'] > 0 ? true : false);
    }

    /**
     * Get current user authentication
     * 
     * Usage:
     * 
     * <code>$user = \Autho\Auth::instance()->get();</code>
     *
     * @access  public
     * @param   string  $name optional key value, return all if $name is null
     * @return  object
     */
    public function get($name = null)
    {
        if (is_null($name))
        {
            return (object) $this->data;
        }
        elseif (array_key_exists($name, $this->data))
        {
            return $this->data[$name];
        }

        return null;
    }

}