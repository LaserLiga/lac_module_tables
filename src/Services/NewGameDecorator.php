<?php

namespace LAC\Modules\Tables\Services;

use App\Http\Controllers\NewGame;
use LAC\Modules\Core\ControllerDecoratorInterface;
use LAC\Modules\Tables\Models\Table;
use Lsr\Interfaces\ControllerInterface;

class NewGameDecorator implements ControllerDecoratorInterface
{
    /** @var NewGame */
    private ControllerInterface $controller;

    public function setController(ControllerInterface $controller): static {
        $this->controller = $controller;
        return $this;
    }

    public function init(): void {

    }

    public function decorates(string $method): bool {
        return match ($method) {
            'show' => true,
            default => false,
        };
    }

    public function decorateShow(): void {
        $this->controller->params->tables = Table::getAll();
        $this->controller->params->tablesCols = 1;
        $this->controller->params->tablesRows = 1;
        foreach ($this->controller->params->tables as $table) {
            $endCol = $table->grid->col + $table->grid->width - 1;
            if ($this->controller->params->tablesCols < $endCol) {
                $this->controller->params->tablesCols = $endCol;
            }
            $endRow = $table->grid->row + $table->grid->height - 1;
            if ($this->controller->params->tablesRows < $endRow) {
                $this->controller->params->tablesRows = $endRow;
            }
        }
        usort($this->controller->params->tables, static function (Table $tableA, Table $tableB) {
            if (is_numeric($tableA->name) && is_numeric($tableB->name)) {
                return (int)$tableA->name - (int)$tableB->name;
            }
            return strcmp($tableA->name, $tableB->name);
        });
        $this->controller->params->addJs[] = 'modules/tables/newGame.js';
        $this->controller->params->addCss[] = 'modules/tables/newGame.css';

        $this->controller->hookedTemplates->import[] = dirname(__DIR__, 2) . '/templates/newGame.latte';
        $this->controller->hookedTemplates->offcanvas[] = 'tablesOffcanvas';
        $this->controller->hookedTemplates->vestsControl[] = 'tablesVests';
    }
}
