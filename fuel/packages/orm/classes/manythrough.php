<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package		Fuel
 * @version		1.0
 * @author		Fuel Development Team
 * @license		MIT License
 * @copyright	2010 - 2011 Fuel Development Team
 * @link		http://fuelphp.com
 */

namespace Orm;

class ManyThrough extends Relation {

	protected $key_from = array('id');

	protected $key_to = array('id');

	/**
	 * @var  string  classname of model to use as connection
	 */
	protected $model_through;

	/**
	 * @var  string  foreign key of from model in connection table
	 */
	protected $key_through_from;

	/**
	 * @var  string  foreign key of to model in connection table
	 */
	protected $key_through_to;

	public function __construct($from, $name, array $config)
	{
		$this->name          = $name;
		$this->model_from    = $from;
		$this->model_to      = array_key_exists('model_to', $config) ? $config['model_to'] : \Inflector::get_namespace($from).'Model_'.\Inflector::classify($name);
		$this->key_from    = array_key_exists('key_from', $config) ? (array) $config['key_from'] : $this->key_from;
		$this->key_to      = array_key_exists('key_to', $config) ? (array) $config['key_to'] : $this->key_to;

		if ( ! empty($config['model_through']))
		{
			$this->model_through = $config['model_through'];
		}
		else
		{
			$table_name = array($this->model_from, $this->model_to);
			natcasesort($table_name);
			$table_name = array_merge($table_name);
			$this->model_through = \Inflector::tableize($table_name[0]).'_'.\Inflector::tableize($table_name[1]);
		}
		$this->key_through_from = ! empty($config['key_through_from'])
			? (array) $config['key_through_from'] : (array) \Inflector::foreign_key($this->model_from);
		$this->key_through_to = ! empty($config['key_through_to'])
			? (array) $config['key_through_to'] : (array) \Inflector::foreign_key($this->model_to);

		$this->cascade_save    = array_key_exists('cascade_save', $config) ? $config['cascade_save'] : $this->cascade_save;
		$this->cascade_delete  = array_key_exists('cascade_save', $config) ? $config['cascade_save'] : $this->cascade_delete;

		if ( ! class_exists($this->model_to))
		{
			throw new Exception('Related model not found by Many_Through relation "'.$this->name.'": '.$this->model_to);
		}
	}

	public function get(Model $from)
	{
		// Create the query on the model_through
		$rel = call_user_func(array($this->model_from, 'relations'), $this->model_through);
		$query = call_user_func(array($rel->model_to, 'find'));

		// set the model_from's keys as where conditions for the model_through
		reset($this->key_through_from);
		foreach ($this->key_from as $key)
		{
			$query->where(current($this->key_through_from), $from->{$key});
			next($this->key_through_from);
		}

		// fetch related model_to on the model_through
		$query->related($this->name);
		$result = $query->get();

		// relate the model_through instances to the model_from
		$rels = $from->_relate();
		$rels[$this->model_through] = $result;
		$from->_relate($rels);

		// return the array of model_to
		$return = array();
		foreach ($result as $r)
		{
			$return[$r->{$this->name}->implode_pk($r->{$this->name})] = $r->{$this->name};
		}
		return $return;
	}

	public function select_through($table)
	{
		$i = 1;
		$rel = call_user_func(array($this->model_from, 'relations'), $this->model_through);
		$props = call_user_func(array($rel->model_to, 'properties'));
		foreach ($props as $pk => $pv)
		{
			$properties[] = array($table.'.'.$pk, $table.'_c'.$i);
			$i++;
		}

		return $properties;
	}

	public function join($alias_from, $rel_name, $alias_to_nr)
	{
		$alias_to = 't'.$alias_to_nr;

		$rel = call_user_func(array($this->model_from, 'relations'), $this->model_through);
		$through_table = call_user_func(array($rel->model_to, 'table'));

		$models = array(
			array(
				'model'      => $rel->model_to,
				'table'      => array($through_table, $alias_to.'_through'),
				'join_type'  => 'left',
				'join_on'    => array(),
				'columns'    => $this->select_through($alias_to.'_through'),
				'rel_name'   => $this->model_through,
				'relation'   => $this
			),
			array(
				'model'      => $this->model_to,
				'table'      => array(call_user_func(array($this->model_to, 'table')), $alias_to),
				'join_type'  => 'left',
				'join_on'    => array(),
				'columns'    => $this->select($alias_to),
				'rel_name'   => $rel_name,
				'relation'   => $this
			)
		);

		reset($this->key_from);
		foreach ($this->key_through_from as $key)
		{
			$models[0]['join_on'][] = array($alias_from.'.'.current($this->key_from), '=', $alias_to.'_through.'.$key);
			next($this->key_from);
		}

		reset($this->key_to);
		foreach ($this->key_through_to as $key)
		{
			$models[1]['join_on'][] = array($alias_to.'_through.'.$key, '=', $alias_to.'.'.current($this->key_to));
			next($this->key_to);
		}

		return $models;
	}

	public function save($model_from, $models_to, $original_model_ids, $parent_saved, $cascade)
	{
		if ( ! $parent_saved)
		{
			return;
		}

		// TODO:
		// NOT YET IMPLEMENTED, MANY-THROUGH RELATIONS ARE ONLY SAVEABLE THROUGH
		// THE OBJECT IN BETWEEN

		$cascade = is_null($cascade) ? $this->cascade_save : (bool) $cascade;
		if ($cascade and ! empty($models_to))
		{
			foreach ($models_to as $m)
			{
				$m->save();
			}
		}
	}

	public function delete($model_from, $models_to, $parent_deleted, $cascade)
	{
		if ( ! $parent_deleted)
		{
			return;
		}

		// break all existing relations
		$model_from->unfreeze();
		$rels = $model_from->_relate();
		$rels[$this->name] = array();
		$model_from->_relate($rels);
		$model_from->freeze();

		// TODO:
		// May need to delete all through models as well, but could also be considered desirable cascading behavior

		$cascade = is_null($cascade) ? $this->cascade_delete : (bool) $cascade;
		if ($cascade and ! empty($models_to))
		{
			foreach ($models_to as $m)
			{
				$m->delete();
			}
		}
	}
}

///* End of file manymany.php */