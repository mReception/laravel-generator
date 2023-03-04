<?php

namespace InfyOm\Generator\Utils;

use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;

class ResponseUtil
{
    public static function makeResponse(string $message, mixed $data): array
    {

        if (!is_array($data) && !is_string($data) && ($data->resource instanceof AbstractPaginator || $data->resource instanceof AbstractCursorPaginator)) {
            return array_merge(
                $data->resource->toArray(),[
                'success' => true,
                'message' => $message,
            ]);
        }

        return [
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ];
    }

    public static function makeError(string $message, array $data = []): array
    {
        $res = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($data)) {
            $res['data'] = $data;
        }

        return $res;
    }
}
