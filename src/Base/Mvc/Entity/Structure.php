<?php

namespace Base\Mvc\Entity;

class Structure
{
	public $shortName;
	public $table;
	public $primaryKey;
	public $columns = [];
	public $relations = [];

	public $columnAliases = [];
}