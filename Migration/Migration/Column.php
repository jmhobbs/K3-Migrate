<?php

	class Migration_Column {
		protected $name;
		protected $type;

		protected $traits;

		protected $size;
		protected $default;
		protected $null;
		protected $comment;

		protected static $types = array(
			'string' => array(
				'type' => 'VARCHAR(%d)',
				'size' => 255,
				'null' => true,
				'default' => null,
				'comment' => null,
				'traits' => array()
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
				)
			)
		);

		public function __construct( $name, $type, $traits = null ) {
			$this->name    = $name;
			$this->type    = $type;
			$this->size    = self::$types[$type]['size'];
			$this->null    = self::$types[$type]['null'];
			$this->default = self::$types[$type]['default'];
			$this->default = self::$types[$type]['comment'];

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
			$sql = "`{$this->name}` " . sprintf( self::$types[$this->type]['type'], $this->size ) . ' ';

			$traits = array();

			foreach( $this->traits as $key => $trait ) {
				if( $trait === true && array_key_exists( $key, self::$types[$this->type]['traits'] ) ) {
					$traits[] = self::$types[$this->type]['traits'][$key];
				}
			}

			if( ! $this->null ) { $traits[] = 'NOT NULL'; }
			if( ! is_null( $this->default ) ) { $traits[] = "DEFAULT '{$this->default}'"; } //! TODO: Escaping here?
			if( ! is_null( $this->comment ) ) { $traits[] = "COMMENT '{$this->comment}'"; } //! TODO: Escaping here?

			return $sql . implode( ' ', $traits );
		}
	}