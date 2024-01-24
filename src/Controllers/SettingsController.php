<?php

namespace LAC\Modules\Tables\Controllers;

use App\Core\App;
use JsonException;
use LAC\Modules\Tables\Models\Table;
use Lsr\Core\Controllers\Controller;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Core\Requests\Request;
use Lsr\Exceptions\TemplateDoesNotExistException;
use Lsr\Logging\Exceptions\DirectoryCreationException;

class SettingsController extends Controller
{
	/**
	 * @return void
	 * @throws ValidationException
	 * @throws TemplateDoesNotExistException
	 */
	public function tables(): void {
		$this->params['addJs'] = ['modules/tables/settings.js'];
		$this->params['tables'] = Table::getAll();
		$this->params['cols'] = 1;
		$this->params['rows'] = 1;
		foreach ($this->params['tables'] as $table) {
			$endCol = $table->grid->col + $table->grid->width - 1;
			if ($this->params['cols'] < $endCol) {
				$this->params['cols'] = $endCol;
			}
			$endRow = $table->grid->row + $table->grid->height - 1;
			if ($this->params['rows'] < $endRow) {
				$this->params['rows'] = $endRow;
			}
		}
		$this->view('../modules/Tables/templates/settings');
	}

	/**
	 * @param Request $request
	 * @return never
	 * @throws JsonException
	 * @throws ValidationException
	 */
	public function addTable(Request $request): never {
		$table = new Table();
		$table->name = lang('Stůl');
		if (!$table->save()) {
			if ($request->isAjax()) {
				$this->respond(['error' => 'Failed to create the table'], 500);
			}
			$request->passErrors[] = lang('Nepodařilo se vytvořit objekt', context: 'errors');
			App::redirect(['settings', 'tables'], $request);
		}

		if ($request->isAjax()) {
			$this->respond(['status' => 'ok']);
		}
		$request->passNotices[] = ['type' => 'success', 'content' => lang('Úspěšně vytvořeno')];
		App::redirect(['settings', 'tables'], $request);
	}

	/**
	 * @param Table $table
	 * @param Request $request
	 * @return never
	 * @throws JsonException
	 */
	public function deleteTable(Table $table, Request $request): never {
		if (!$table->delete()) {
			if ($request->isAjax()) {
				$this->respond(['error' => 'Failed to delete the table'], 500);
			}
			$request->passErrors[] = lang('Nepodařilo se smazat objekt', context: 'errors');
			App::redirect(['settings', 'tables'], $request);
		}

		if ($request->isAjax()) {
			$this->respond(['status' => 'ok']);
		}
		$request->passNotices[] = ['type' => 'success', 'content' => lang('Úspěšně smazáno')];
		App::redirect(['settings', 'tables'], $request);
	}

	public function saveTables(Request $request): never {
		/** @var array<numeric, array{name:string,grid_col?:numeric,grid_row?:numeric,grid_width?:numeric,grid_height?:numeric}> $tables */
		$tables = $request->post['table'] ?? [];
		foreach ($tables as $id => $tableInfo) {
			try {
				$table = Table::get((int)$id);
			} catch (ModelNotFoundException) {
				$request->errors[] = sprintf(lang('Table #%d was not found', context: 'errors'), $id);
				continue;
			} catch (ValidationException|DirectoryCreationException $e) {
				continue;
			}

			$table->name = $tableInfo['name'];
			$table->grid->col = (int)($tableInfo['grid_col'] ?? 1);
			$table->grid->row = (int)($tableInfo['grid_row'] ?? 1);
			$table->grid->width = (int)($tableInfo['grid_width'] ?? 1);
			$table->grid->height = (int)($tableInfo['grid_height'] ?? 1);

			if (!$table->save()) {
				$request->errors[] = sprintf(lang('Failed to save table #%d', context: 'errors'), $id);
			}
		}

		if (empty($request->errors)) {
			if ($request->isAjax()) {
				$this->respond(['status' => 'ok']);
			}
			$request->passNotices[] = ['type' => 'success', 'content' => lang('Úspěšně uloženo')];
			App::redirect(['settings', 'tables'], $request);
		}

		if ($request->isAjax()) {
			$this->respond(['errors' => $request->errors], 500);
		}

		$request->passErrors = $request->errors;
		App::redirect(['settings', 'tables'], $request);
	}
}