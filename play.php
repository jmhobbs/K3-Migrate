<?php

	//! TODO: Merge arg arrays with defaults instead of the one-off testing with isset

	// http://guides.rubyonrails.org/migrations.html#creating-a-table

	abstract class Migration_Statement {
		abstract public function toSQL ();
	}

	class Migration_Column {
		protected $name;
		protected $type;

		protected $traits;

		protected $size;
		protected $default;
		protected $null;

		protected static $types = array(
			'string' => array(
				'type' => 'VARCHAR(%d)',
				'size' => 255,
				'null' => true,
				'default' => null,
				'traits' => array()
			),
			'integer' => array(
				'type' => 'INTEGER(%d)',
				'size' => 10,
				'null' => true,
				'default' => null,
				'traits' => array(
					'unsigned'       => 'UNSIGNED',
					'auto_increment' => 'AUTO_INCREMENT',
				)
			)
		);

		public function __construct( $name, $type, $traits = null ) {
			$this->name    = $name;
			$this->type    = $type;
			$this->size    = self::$types[$type]['size'];
			$this->null    = self::$types[$type]['null'];
			$this->default = self::$types[$type]['default'];

			$this->traits  = array();

			if( ! is_null( $traits ) ) {
				foreach( $traits as $key => $trait ) {
					switch( $key ) {
						case 'size':
							$this->size = intval( $trait );
							break;
						case 'null':
							$this->null = $trait;
							break;
						case 'default':
							$this->default = $trait;
							break;
						default:
							$this->traits[$key] = $trait;
							break;
					}
				}
			}
		}

		public function toSQL () {
			$sql = "`{$this->name}` " . sprintf( self::$types[$this->type]['type'], $this->size ) . ' ';

			$traits = array();

			if( ! $this->null ) { $traits[] = 'NOT NULL'; }
			if( ! is_null( $this->default ) ) { $traits[] = "DEFAULT '{$this->default}'"; } //! TODO: Escaping here?

			foreach( $this->traits as $key => $trait ) {
				if( $trait === true && array_key_exists( $key, self::$types[$this->type]['traits'] ) ) {
					$traits[] = self::$types[$this->type]['traits'][$key];
				}
			}

			return $sql . implode( ' ', $traits );
		}
	}

	class Migration_CreateTable extends Migration_Statement {

		protected $_tableName;
		protected $_engine;
		protected $_charset;

		protected $_columns = array();
		protected $_primaryKey = null;

		/**
		* Options:
		*      id  => false/string   - Do not create an automatic id column
		* created  => false/string   - Created column name || false == no column
		* modified => false/string   - Modified column name || false == no column
		*   engine => string
		*  charset => string
		*/
		public function __construct ( $tableName, $args = null ) {
			$this->_tableName = $tableName;

			$defaults = array(
				'id'       => 'id',
				'created'  => 'created',
				'modified' => 'modified',
				'engine'   => 'InnoDB',
				'charset'  => 'utf8',
			);

			if( is_array( $args ) ) { $args = array_merge( $defaults, $args ); }
			else { $args = $defaults; }

			if( false !== $args['id'] ) {
				$this->addColumn( 'integer', $args['id'], array( 'null' => false, 'unsigned' => true, 'auto_increment' => true ) );
				$this->primaryKey( $args['id'] );
			}

			if( false !== $args['created'] ) {
				$this->addColumn( 'integer', $args['created'], array( 'null' => false, 'unsigned' => true ) );
			}

			if( false !== $args['modified'] ) {
				$this->addColumn( 'integer', $args['modified'], array( 'null' => false ) );
			}

			$this->engine( $args['engine'] );
			$this->charset( $args['charset'] );
		}

		public function toSQL () {
			$sql = "CREATE TABLE `{$this->_tableName}` (\n\t";

			$rows = array();

			foreach( $this->_columns as $column ) {
				$rows[] = $column->toSQL();
			}

			if( ! is_null( $this->_primaryKey ) ) {
				$rows[] = "PRIMARY KEY( `{$this->_primaryKey}` )";
			}

			$sql .= implode( ",\n\t", $rows );

			$sql .= "\n) ENGINE={$this->_engine} DEFAULT CHARSET={$this->_charset};\n";
			return $sql;
		}

		public function engine ( $engine ) { $this->_engine = $engine; }
		public function charset ( $charset ) { $this->_charset = $charset; }
		public function primaryKey ( $columnName ) { $this->_primaryKey = $columnName; }
		public function tableName ( $tableName ) { $this->_tableName = $tableName; }

		public function addColumn ( $type, $name, $traits = null ) {
			$this->_columns[$name] = new Migration_Column( $name, $type, $traits );
		}
	}

	class Migration {

		public static function CreateTable ( $name, $args = null ) {
			return new Migration_CreateTable( $name, $args );
		}

	}

	$table = Migration::CreateTable( 'User', array( 'modified' => false ) );
	$table->addColumn( 'string', 'name', array( 'default' => 'John' ) );
	die( $table->toSQL() );