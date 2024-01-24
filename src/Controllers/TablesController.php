<?php

namespace LAC\Modules\Tables\Controllers;

use JsonException;
use LAC\Modules\Tables\Models\Table;
use Lsr\Core\Controllers\Controller;
use Lsr\Core\Exceptions\ValidationException;

class TablesController extends Controller
{

	/**
	 * @param Table $table
	 * @return never
	 * @throws JsonException
	 * @throws ValidationException
	 */
	public function cleanTable(Table $table): never {
		if (!$table->clean()) {
			$this->respond(['error' => 'Clean failed'], 500);
		}

		$this->respond(['status' => 'ok']);
	}

	/**
	 * @param Table $table
	 * @return never
	 * @throws JsonException
	 */
	public function get(Table $table): never {
		$this->respond($table);
	}

	/**
	 * @return never
	 * @throws ValidationException
	 * @throws JsonException
	 */
	public function getAll(): never {
		$this->respond(['tables' => array_values(Table::getAll())]);
	}

}