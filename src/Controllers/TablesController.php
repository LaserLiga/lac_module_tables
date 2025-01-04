<?php

namespace LAC\Modules\Tables\Controllers;

use LAC\Modules\Tables\Models\Table;
use Lsr\Core\Controllers\Controller;
use Lsr\ObjectValidation\Exceptions\ValidationException;
use Lsr\Core\Requests\Dto\ErrorResponse;
use Lsr\Core\Requests\Enums\ErrorType;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;

/**
 *
 */
class TablesController extends Controller
{
    /**
     * @param Table $table
     *
     * @return ResponseInterface
     * @throws ValidationException
     */
    #[OA\Post('/tables/{id}/clean')]
    #[OA\Parameter(name: 'id', description: 'Table ID', in: 'path', required: true)]
    #[OA\Response(response: 200, description: 'Table cleaned', content: new OA\JsonContent(ref: '#/components/schemas/Table'))]
    #[OA\Response(response: 500, description: 'Error', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'))]
    public function cleanTable(Table $table): ResponseInterface {
        if (!$table->clean()) {
            return $this->respond(new ErrorResponse('Clean failed', ErrorType::INTERNAL), 500);
        }

        return $this->respond($table);
    }

    #[OA\Get('/tables/{id}')]
    #[OA\Parameter(name: 'id', description: 'Table ID', in: 'path', required: true)]
    #[OA\Response(response: 200, description: 'Table cleaned', content: new OA\JsonContent(ref: '#/components/schemas/Table'))]
    #[OA\Response(response: 404, description: 'Table not found', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'))]
    public function get(Table $table): ResponseInterface {
        return $this->respond($table);
    }


    #[OA\Get('/tables')]
    #[OA\Response(response: 200, description: 'All tables', content: new OA\JsonContent(ref: '#/components/schemas/TablesList'))]
    public function getAll(): ResponseInterface {
        return $this->respond(['tables' => array_values(Table::getAll())]);
    }
}
