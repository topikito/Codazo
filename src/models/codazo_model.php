<?php

/**
 * Description of codazo
 *
 * @author robertopereznygaard
 */
class CodazoModel extends CodazoObject
{

	protected $_tableName;
	protected $_app;

	public function __construct()
	{
		$this->_app = self::$_application;
	}

	/**
	 *
	 * Conditions must be specified in the following structure:
	 * 		array('key' => value)
	 * 			- This implies '=' condition
	 * 		array(array('key','value',<optional> 'type of comparison'))
	 * 			- This constructs "key" "type" "value"
	 *
	 * @param array $conditions
	 */
	public function get($conditions = array())
	{
		$sql = 'SELECT * FROM ' . $this->_tableName;

		if (!is_array($conditions) || empty($conditions))
		{
			return false;
		}

		$sqlConditions = $sqlValues = array();
		$type = '=';
		foreach ($conditions as $key => $value)
		{
			if (is_array($value))
			{
				list($key, $value, $optionalType) = $value;

				if (!empty($optionalType))
				{
					$type = $optionalType;
				}
			}
			$sqlConditions[] = '(' . $key . ' ' . $type . ' ?)';
			$sqlValues[] = $value;
		}
		$sql .= ' WHERE ';
		$sql .= implode(' AND ', $sqlConditions);

		$result = $this->_app['db']->fetchAssoc($sql, $sqlValues);
		return $result;
	}

	public function save($values)
	{
		if (!is_array($values) || empty($values))
		{
			return false;
		}

		$this->_app['db']->insert($this->_tableName, $values);
		$insertedId = $this->_app['db']->lastInsertId();

		return $insertedId;
	}

	public function update($newValues, $whereValues)
	{
		if (!is_array($newValues) || !is_array($whereValues) || empty($newValues) || empty($whereValues))
		{
			return false;
		}

		$result = $this->_app['db']->update($this->_tableName, $newValues, $whereValues);

		return $result;
	}

	public function begin()
	{
		return $this->_app['db']->beginTransaction();
	}

	public function commit()
	{
		return $this->_app['db']->commit();
	}

}
