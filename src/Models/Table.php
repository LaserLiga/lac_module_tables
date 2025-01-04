<?php

namespace LAC\Modules\Tables\Models;

use App\Models\BaseModel;
use App\Models\GameGroup;
use DateTimeInterface;
use LAC\Modules\Tables\DataObjects\Grid;
use Lsr\ObjectValidation\Exceptions\ValidationException;
use Lsr\Orm\Attributes\Instantiate;
use Lsr\Orm\Attributes\PrimaryKey;
use Lsr\Orm\Attributes\Relations\OneToOne;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(schema: "Table", type: 'object')]
#[PrimaryKey('id_table')]
class Table extends BaseModel
{
    public const string TABLE = 'tables';

    #[Property]
    public string $name;

    #[Instantiate, Property]
    public Grid $grid;

    #[OneToOne, Property]
    public ?GameGroup $group = null;

    /**
     * Create a group
     *
     * @param  bool  $overwrite
     * @param  DateTimeInterface|null  $date
     *
     * @return GameGroup
     * @throws ValidationException
     */
    public function createGroup(bool $overwrite = false, ?DateTimeInterface $date = null) : GameGroup {
        if (!$overwrite && isset($this->group)) {
            // Prevent creating multiple groups when there is already one
            return $this->group;
        }

        if ($overwrite && isset($this->group)) {
            $this->clean(); // Close the existing group
        }

        $this->group = new GameGroup();
        $this->group->name = sprintf(
          lang('Stůl %s', context: 'tables').' - %s',
          $this->name,
          isset($date) ? $date->format('d.m.Y H:i') : date('d.m.Y H:i')
        );
        $this->group->active = true;
        $this->group->save();

        $this->save();

        return $this->group;
    }

    /**
     * "Clean" the table -> Remove its group
     *
     * @return bool
     * @throws ValidationException
     */
    public function clean() : bool {
        if (isset($this->group)) {
            $this->group->active = false;
            $this->group->save();
        }
        $this->group = null;
        return $this->save();
    }
}
