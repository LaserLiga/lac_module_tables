<?php

namespace LAC\Modules\Tables\Services;

use App\GameModels\Game\Game;
use App\Tools\AbstractResultsParser;
use LAC\Modules\Core\ResultParserExtensionInterface;
use LAC\Modules\Tables\Models\Table;
use Lsr\Core\Exceptions\ModelNotFoundException;

class ResultParserExtension implements ResultParserExtensionInterface
{

	public function parse(Game $game, array $meta, AbstractResultsParser $parser): void {
		// Assign game to the table
		if (!empty($meta['table'])) {
			try {
				$table = Table::get((int)$meta['table']);
				$game->table = $table;
				if (!isset($table->group)) {
					// Assign a group to the table if it doesn't have any
					if (isset($game->group)) {
						// Copy group from game
						$table->group = $game->group;
					}
					else {
						// Create a new group for the table
						$game->group = $table->createGroup(date: $game->start);
					}
				}
				else if (!isset($game->group)) {
					$game->group = $table->group;
				}
			} catch (ModelNotFoundException) {
				// Ignore
			}
		}
	}
}