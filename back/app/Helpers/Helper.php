<?php

namespace App\Helpers;

class Helper
{
    public static function formatDataNullable(array &$data)
    {
        $data = collect($data)->map(function ($field) {
            if (is_array($field)) {
                return self::formatDataNullable($field);
            }
            return 'null' == $field ? null : $field;
        })->toArray();
        return $data;
    }
}
