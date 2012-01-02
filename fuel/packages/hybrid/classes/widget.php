<?php

/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.1
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Hybrid;

/**
 * Hybrid 
 * 
 * A set of class that extends the functionality of FuelPHP without 
 * affecting the standard workflow when the application doesn't actually 
 * utilize Hybrid feature.
 * 
 * @package     Fuel
 * @subpackage  Hybrid
 * @category    Widget
 * @author      Mior Muhammad Zaki <crynobone@gmail.com>
 */

class Widget 
{
	/**
	 * Cache Tab instance so we can reuse it on multiple request.
	 * 
	 * @static
	 * @access  protected
	 * @var     array
	 */
	protected static $instances = array();

	/**
	 * Load the configuration before anything else.
	 *
	 * @static
	 * @access  public
	 */
	public static function _init()
	{
		\Config::load('hybrid', 'hybrid');
	}

	/**
	 * Initiate a new Tab instance.
	 * 
	 * @static
	 * @access  public
	 * @param   string  $name
	 * @param   array   $config
	 * @return  Tab
	 * @throws  \FuelException
	 */
	public static function __callStatic($method, array $arguments)
	{
		if ( ! in_array($method, array('factory', 'forge', 'instance', 'make')))
		{
			throw new \FuelException(__CLASS__.'::'.$method.'() does not exist.');
		}

		foreach (array(null, array()) as $key => $default)
		{
			isset($arguments[$key]) or $arguments[$key] = $default;
		}

		list($instance_name, $config) = $arguments;
		
		$instance_name = $instance_name ?: '.default';
		$instance_name = strtolower($instance_name);
		
		list($type, $name) = explode('.', $instance_name, 2);

		if ( ! isset(static::$instances[$instance_name]))
		{
			$driver = "\Hybrid\Widget_".ucfirst($type);

			if (class_exists($driver))
			{
				static::$instances[$instance_name] = new $driver($name, $config);
			}
			else
			{
				throw new \FuelException("Requested {$driver} does not exist.");
			}
		}

		return static::$instances[$instance_name];
	}
}