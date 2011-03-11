<?php

	abstract class Kohana_Migration_Statement {
		abstract public function toSQL ();
	}