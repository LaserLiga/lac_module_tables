<?php

namespace LAC\Modules\Tables\Api\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema    : 'TablesList',
    properties: [
    'tables' => new OA\Property(
        'tables',
        type: 'array',
        items: new OA\Items(
            ref: '#/components/schemas/Table'
        ),
    )

    ],
    type      : 'object'
)]
class TablesListResponse
{
}
