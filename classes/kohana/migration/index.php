<?php

/*
CREATE [UNIQUE|FULLTEXT|SPATIAL] INDEX index_name
	[index_type]
	ON tbl_name (index_col_name,...)
	[index_type]

index_col_name:
	col_name [(length)] [ASC | DESC]

index_type:
	USING {BTREE | HASH}
*/


class Kohana_Migration_Index extends Kohana_Migration_Statement {
	/**
	 * @var string
	 */
	protected $_name;
	/**
	 * @var array
	 */
	protected $_columns;
	/**
	 * @var array
	 */
	protected $_traits;

	protected static $variantTraits = array(
		'index' => '',
		'unique' => 'UNIQUE',
		'fulltext' => 'FULLTEXT',
		'spatial' => 'SPATIAL'
	);

	protected static $typeTraits = array(
		'btree' => 'USING BTREE',
		'hash' => 'USING HASH'
	);

	public function __construct($columns, $traits = null)
	{
		$this->_columns = $columns;

		$default_traits = array(
			'name' => null
		);

		// Set up traits
		if (is_array($traits))
		{
			$this->_traits = array_merge($default_traits, $traits);
		}
		else
		{
			$this->_traits = $default_traits;
		}

		// Get the name, if needed
		if (is_null($this->_traits['name']))
		{
			$this->_name = $this->_generateName();
		}
		else
		{
			$this->_name = $this->_traits['name'];
		}

	}

	public function _generateName()
	{
		return "index_".implode('_', array_keys($this->_columns));
	}

	public static function isType($type)
	{
		return array_key_exists($type, self::$variantTraits);
	}

	public function toSQL()
	{
		$variant = '';
		foreach ($this->_traits as $trait)
		{
			if (array_key_exists($trait, self::$variantTraits))
			{
				$variant = self::$variantTraits[$trait];
			}
		}

		$type = '';
		foreach ($this->_traits as $trait)
		{
			if (array_key_exists($trait, self::$typeTraits))
			{
				$type = self::$typeTraits[$trait];
			}
		}

		$sql = "$variant INDEX `{$this->_name}` (";

		$keys = array();
		foreach ($this->_columns as $column => $traits)
		{
			$key = "`{$column}` ";
			if (isset($traits['length']))
			{
				$key .= '('.intval($traits['length']).') ';
			}
			if (isset($traits['order']))
			{
				if ($traits['order'] == 'asc')
				{
					$key .= 'ASC ';
				}
				elseif ($traits['order'] == 'desc')
				{
					$key .= 'DESC ';
				}
			}
			$keys[] = trim($key);
		}
		$sql .= implode(', ', $keys).") $type";

		return $sql;
	}

	public function getName()
	{
		return $this->_name;
	}
}
