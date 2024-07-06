<?php

namespace LAC\Modules\Tables\Services;

use App\GameModels\Game\Game;
use App\GameModels\Game\Player;
use App\GameModels\Game\Team;
use Dibi\Row;
use LAC\Modules\Core\GameDataExtensionInterface;
use LAC\Modules\Tables\Models\Table;
use Lsr\Core\Exceptions\ModelNotFoundException;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Logging\Exceptions\DirectoryCreationException;

class GameDataExtension implements GameDataExtensionInterface
{
    public function init(Game|Team|Player $game): void {
    }

    public function parseRow(Row $row, Game|Team|Player $game): void {
        $table = null;
        if (isset($row->id_table)) {
            try {
                $table = Table::get((int)$row->id_table);
            } catch (ModelNotFoundException | ValidationException | DirectoryCreationException) {
            }
        }
        $game->table = $table;
    }

    /**
     * @inheritDoc
     */
    public function addQueryData(array &$data, Game|Team|Player $game): void {
        $data['id_table'] = $game->table?->id;
    }

    public function addJsonData(array &$data, Game|Team|Player $game): void {
        if (isset($game->table)) {
            $data['table'] = $game->table;
        }
    }

    public function save(Game|Team|Player $game): bool {
        return true;
    }
}
