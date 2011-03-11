<?php

	class Migration_Column {
		protected $name;
		protected $type;

		protected $traits;

		protected $size;
		protected $default;
		protected $null;
		protected $comment;

		//:float, :decimal, :time, :date, :binary, :boolean.

		protected static $types = array(
			'string' => array(
				'type' => 'VARCHAR(%d)',
				'size' => 255,
				'null' => true,
				'default' => null,
				'comment' => null,
				'traits' => array(),
				'default_traits' => array()
			),
			'integer' => array(
				'type' => 'INTEGER(%d)',
				'size' => 10,
				'null' => true,
				'default' => null,
				'comment' => null,
				'traits' => array(
					'unsigned'       => 'UNSIGNED',
					'auto_increment' => 'AUTO_INCREMENT',
				),
				'default_traits' => array()
			),
			// TODO: http://www.ispirer.com/doc/sqlways39/Output/SQLWays-1-211.html
			'text' => array(
				'type' => 'TEXT',
				'size' => null,
				'null' => true,
				'default' => null,
				'comment' => null,
				'traits' => array(),
				'default_traits' => array()
			),
			'blob' => array(
				'type' => 'TEXT',
				'size' => null,
				'null' => true,
				'default' => null,
				'comment' => null,
				'traits' => array(),
				'default_traits' => array()
			),
			'datetime' => array(
				'type' => 'DATETIME',
				'size' => null,
				'null' => true,
				'default' => null,
				'comment' => null,
				'traits' => array(),
				'default_traits' => array()
			),
			'timestamp' => array(
				'type' => 'INTEGER(10)', // Why not TIMESTAMP? Because Kohana defaults to INT(10)
				'size' => null,
				'null' => false,
				'default' => '0',
				'comment' => null,
				'traits' => array(
					'unsigned' => 'UNSIGNED',
				),
				'default_traits' => array(
					'unsigned' => true
				)
			),
			'decimal' => array(
				'type' => 'DECIMAL(%d,%d)',
				'size' => array( 5, 2 ),
				'null' => true,
				'default' => null,
				'comment' => null,
				'traits' => array(),
				'default_traits' => array()
			),
			'float' => array(
				'type' => 'FLOAT(%d,%d)',
				'size' => array( 5, 2 ),
				'null' => true,
				'default' => null,
				'comment' => null,
				'traits' => array(),
				'default_traits' => array()
			),
		);

		public function __construct( $name, $type, $traits = null ) {
			$this->name    = $name;
			$this->type    = $type;
			$this->size    = self::$types[$type]['size'];
			$this->null    = self::$types[$type]['null'];
			$this->default = self::$types[$type]['default'];
			$this->comment = self::$types[$type]['comment'];

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
						case 'comment':
							$this->comment = $trait;
							break;
						default:
							$this->traits[$key] = $trait;
							break;
					}
				}
			}
		}

		public function toSQL () {

			$chunks = array(
				"`{$this->name}`",
				vsprintf( self::$types[$this->type]['type'], $this->size )
			);

			$requested_traits = array_merge( self::$types[$this->type]['default_traits'], $this->traits );
			foreach( $requested_traits as $key => $trait ) {
				if( ( $trait === true && array_key_exists( $key, self::$types[$this->type]['traits'] ) ) ) {
					$chunks[] = self::$types[$this->type]['traits'][$key];
				}
			}

			if( ! $this->null ) { $chunks[] = 'NOT NULL'; }
			if( ! is_null( $this->default ) ) { $chunks[] = "DEFAULT '{$this->default}'"; } //! TODO: Escaping here?
			if( ! is_null( $this->comment ) ) { $chunks[] = "COMMENT '{$this->comment}'"; } //! TODO: Escaping here?

			return implode( ' ', $chunks );
		}
	}