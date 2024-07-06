<?php

namespace LAC\Modules\Tables\Controllers;

use App\Api\Response\ErrorDto;
use App\Api\Response\ErrorType;
use JsonException;
use LAC\Modules\Tables\Models\Table;
use Lsr\Core\Controllers\Controller;
use Lsr\Core\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;

class TablesController extends Controller
{
    /**
     * @param Table $table
     *
     * @return ResponseInterface
     * @throws JsonException
     * @throws ValidationException
     */
    public function cleanTable(Table $table): ResponseInterface {
        if (!$table->clean()) {
            return $this->respond(new ErrorDto('Clean failed', ErrorType::INTERNAL), 500);
        }

        return $this->respond($table);
    }

    /**
     * @param Table $table
     * @return never
     * @throws JsonException
     */
    public function get(Table $table): ResponseInterface {
        return $this->respond($table);
    }

    /**
     * @return ResponseInterface
     * @throws JsonException
     * @throws ValidationException
     */
    public function getAll(): ResponseInterface {
        return $this->respond(['tables' => array_values(Table::getAll())]);
    }
}
