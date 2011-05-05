<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Core;

/**
 * Str class tests
 * 
 * @group Core
 * @group Str
 */
class Tests_Str extends TestCase {
	
	/**
	 * Test for Str::truncate()
	 * 
	 * @test
	 */
	public function test_truncate()
	{
		$limit  = 15;
		$string = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
		
		$output = Str::truncate($string, $limit);
		$expected = 'Lorem ipsum dol...';
		$this->assertEquals($expected, $output);
		
		$output = Str::truncate($string, $limit, '..');
		$expected = 'Lorem ipsum dol..';
		$this->assertEquals($expected, $output);
		
		$string = '<h1>'.$string.'</h1>';
		
		$output = Str::truncate($string, $limit, '...', false);
		$expected = '<h1>Lorem ipsum...';
		$this->assertEquals($expected, $output);
		
		$output = Str::truncate($string, $limit, '...', true);
		$expected = '<h1>Lorem ipsum dol...</h1>';
		$this->assertEquals($expected, $output);
	}
	
	/**
	 * Test for Str::increment()
	 * 
	 * @test
	 */
	public function test_increment()
	{
		$values = array('valueA', 'valueB', 'valueC');
		
		for ($i = 0; $i < count($values); $i ++)
		{
			$output = Str::increment($values[$i], $i);
			$expected = $values[$i].'_'.$i;
			
			$this->assertEquals($expected, $output);
		}
	}
	
	/**
	 * Test for Str::lower()
	 * 
	 * @test
	 */
	public function test_lower()
	{
		$output = Str::lower('HELLO WORLD');
		$expected = "hello world";
		
		$this->assertEquals($expected, $output);
	}
	
	/**
	 * Test for Str::upper()
	 * 
	 * @test
	 */
	public function test_upper()
	{
		$output = Str::upper('hello world');
		$expected = "HELLO WORLD";
		
		$this->assertEquals($expected, $output);
	}
	
	/**
	 * Test for Str::lcfirst()
	 * 
	 * @test
	 */
	public function test_lcfirst()
	{
		$output = Str::lcfirst('Hello World');
		$expected = "hello World";
		
		$this->assertEquals($expected, $output);
	}
	
	/**
	 * Test for Str::ucfirst()
	 * 
	 * @test
	 */
	public function test_ucfirst()
	{
		$output = Str::ucfirst('hello world');
		$expected = "Hello world";

		$this->assertEquals($expected, $output);
	}
	
	/**
	 * Test for Str::ucwords()
	 * 
	 * @test
	 */
	public function test_ucwords()
	{
		$output = Str::ucwords('hello world');
		$expected = "Hello World";

		$this->assertEquals($expected, $output);
	}
	
	/**
	 * Test for Str::random()
	 * 
	 * @test
	 */
	public function test_random()
	{
		// testing length
		$output = Str::random('alnum', 34);
		$this->assertEquals(34, strlen($output));
		
		// testing alnum
		$output = Str::random('alnum', 15);
		$this->assertTrue(ctype_alnum($output));
		
		// testing numeric
		$output = Str::random('numeric', 20);
		$this->assertTrue(ctype_digit($output));
		
		// testing alpha
		$output = Str::random('alpha', 35);
		$this->assertTrue(ctype_alpha($output));
		
		// testing nozero
		$output = Str::random('nozero', 22);
		$this->assertFalse(strpos($output, '0'));	
	}
}