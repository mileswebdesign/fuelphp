<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.1
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Tasks;

/**
 * Setup commandline:
 *      php oil refine fuel
 *
 * @package     Fuel
 * @version     1.1
 * @author      Phil Sturgeon
 */

class Fuel
{
	/**
	 * Send command with runtime option:
	 *      php oil refine fuel --help
	 *
	 * @static
	 * @access  public
	 * @return  void
	 */
	public static function run()
	{
		$help    = \Cli::option('h', \Cli::option('help'));
		$env     = \Cli::option('e', \Cli::option('env'));

		switch (true)
		{
			case ($env !== null and in_array($env, array(\Fuel::DEVELOPMENT, \Fuel::STAGE, \Fuel::TEST, \Fuel::PRODUCTION))) :
				\Cli::spawn("export FUEL_ENV ".$env);
			case $help :
			default :
				static::help();
			break;
		}
	}

	/**
	 * Show help menu
	 *
	 * @static
	 * @access  public
	 * @return  void
	 */
	public static function help()
	{
			echo <<<HELP

Usage:
php oil refine fuel

Runtime options:
-h, [--help]      # Show option

Description:
The 'oil refine autho' command can be used in several ways to facilitate quick development, help with
user database generation and installation

HELP;

	}
	
}