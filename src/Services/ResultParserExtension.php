<?php

namespace LAC\Modules\Tables\Services;

use LAC\Modules\Tables\Models\Table;
use Lsr\Lg\Results\AbstractResultsParser;
use Lsr\Lg\Results\Interface\Models\GameInterface;
use Lsr\Lg\Results\Interface\ResultParserExtensionInterface;
use Lsr\Orm\Exceptions\ModelNotFoundException;

class ResultParserExtension implements ResultParserExtensionInterface
{
    public function parse(GameInterface $game, array $meta, AbstractResultsParser $parser) : void {
        // Assign game to the table
        if (!empty($meta['table'])) {
            try {
                $table = Table::get((int)$meta['table']);
                $game->table = $table;
                if (!isset($table->group)) {
                    // Assign a group to the table if it doesn't have any
                    if ($game->getGroup() !== null) {
                        // Copy group from game
                        $table->group = $game->getGroup();
                    } else {
                        // Create a new group for the table
                        $game->group = $table->createGroup(date: $game->start);
                    }
                } else if ($game->getGroup() === null) {
                    $game->group = $table->group;
                }
            } catch (ModelNotFoundException) {
                // Ignore
            }
        }
    }
}
