<?php

use LAC\Modules\Tables\Controllers\SettingsController;
use LAC\Modules\Tables\Controllers\TablesController;
use Lsr\Core\Routing\Route;

Route::group('settings/tables')
	->get('', [SettingsController::class, 'tables'])->name('settings-tables')
	->post('', [SettingsController::class, 'saveTables'])
	->post('new', [SettingsController::class, 'addTable'])
	->post('{id}/delete', [SettingsController::class, 'deleteTable'])
	->delete('{id}', [SettingsController::class, 'deleteTable']);

Route::group('tables')
	->get('', [TablesController::class, 'getAll'])
	->get('{id}', [TablesController::class, 'get'])
	->post('{id}/clean', [TablesController::class, 'cleanTable']);