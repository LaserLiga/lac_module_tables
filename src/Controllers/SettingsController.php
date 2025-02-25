<?php

namespace LAC\Modules\Tables\Controllers;

use App\Core\Info;
use LAC\Modules\Tables\Models\Table;
use Lsr\Core\Controllers\Controller;
use Lsr\Orm\Exceptions\ModelNotFoundException;
use Lsr\ObjectValidation\Exceptions\ValidationException;
use Lsr\Core\Requests\Dto\ErrorResponse;
use Lsr\Core\Requests\Request;
use Lsr\Exceptions\TemplateDoesNotExistException;
use Lsr\Logging\Exceptions\DirectoryCreationException;
use Psr\Http\Message\ResponseInterface;

/**
 *
 */
class SettingsController extends Controller
{
    /**
     * @return ResponseInterface
     * @throws TemplateDoesNotExistException
     * @throws ValidationException
     */
    public function tables(): ResponseInterface {
        $this->params['addJs'] = ['modules/tables/settings.js'];
        $this->params['tables'] = [];
        $this->params['cols'] = (int) Info::get('table_column_count', 10);
        $this->params['rows'] = (int) Info::get('table_row_count', 10);
        foreach (Table::getAll() as $table) {
            $this->params['tables'][$table->grid->row] ??= [];

            // If duplicate, find first available column
            while (isset($this->params['tables'][$table->grid->row][$table->grid->col])) {
                $table->grid->col++;
            }

            $this->params['tables'][$table->grid->row][$table->grid->col] = $table;

            $endCol = $table->grid->col + $table->grid->width - 1;
            if ($this->params['cols'] < $endCol) {
                $this->params['cols'] = $endCol;
            }
            $endRow = $table->grid->row + $table->grid->height - 1;
            if ($this->params['rows'] < $endRow) {
                $this->params['rows'] = $endRow;
            }
        }
        return $this->view('../modules/Tables/templates/settings');
    }

    /**
     * @param Request $request
     * @return ResponseInterface
     * @throws ValidationException
     */
    public function addTable(Request $request): ResponseInterface {
        $table = new Table();
        $table->name = lang('Stůl');
        if (!$table->save()) {
            if ($request->isAjax()) {
                $this->respond(new ErrorResponse('Failed to create the table'), 500);
            }
            $request->passErrors[] = lang('Nepodařilo se vytvořit objekt', context: 'errors');
            return $this->app->redirect(['settings', 'tables'], $request);
        }

        if ($request->isAjax()) {
            return $this->respond('');
        }
        $request->passNotices[] = ['type' => 'success', 'content' => lang('Úspěšně vytvořeno')];
        return $this->app->redirect(['settings', 'tables'], $request);
    }

    /**
     * @param  Table  $table
     * @param  Request  $request
     * @return ResponseInterface
     */
    public function deleteTable(Table $table, Request $request): ResponseInterface {
        if (!$table->delete()) {
            if ($request->isAjax()) {
                return $this->respond(new ErrorResponse('Failed to delete the table'), 500);
            }
            $request->passErrors[] = lang('Nepodařilo se smazat objekt', context: 'errors');
            return $this->app->redirect(['settings', 'tables'], $request);
        }

        if ($request->isAjax()) {
            return $this->respond('');
        }
        $request->passNotices[] = ['type' => 'success', 'content' => lang('Úspěšně smazáno')];
        return $this->app->redirect(['settings', 'tables'], $request);
    }

    public function saveTables(Request $request): ResponseInterface {
        $columns = (int) $request->getPost('columns', 10);
        Info::set('table_column_count', $columns);
        $rows = (int) $request->getPost('rows', 10);
        Info::set('table_row_count', $rows);

        /** @var array<numeric, array{name:string,grid_col?:numeric,grid_row?:numeric,grid_width?:numeric,grid_height?:numeric}> $tables */
        $tables = $request->getPost('table', []);
        foreach ($tables as $id => $tableInfo) {
            try {
                $table = Table::get((int)$id);
            } catch (ModelNotFoundException) {
                $request->errors[] = sprintf(lang('Table #%d was not found', context: 'errors'), $id);
                continue;
            } catch (ValidationException | DirectoryCreationException $e) {
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
                return $this->respond('');
            }
            $request->passNotices[] = ['type' => 'success', 'content' => lang('Úspěšně uloženo')];
            return $this->app->redirect(['settings', 'tables'], $request);
        }

        if ($request->isAjax()) {
            return $this->respond(
                new ErrorResponse('An error has occured', values: ['errors' => $request->errors]),
                500
            );
        }

        $request->passErrors = $request->errors;
        return $this->app->redirect(['settings', 'tables'], $request);
    }
}
