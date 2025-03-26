<?php

namespace App\Services\Filters;

class BaseFilter
{
    public static function apply(string $condition)
    {
        return function($query) use ($condition) {
            $operators = ['>=', '<=', '>', '<', '=', '!=','LIKE','IN'];
            
            foreach ($operators as $operator) {
                if (strpos($condition, $operator) !== false) {
                    list($field, $value) = explode($operator, $condition, 2);
                    $field = preg_replace('/^\(+|\)+$/', '', trim($field));
                    if($operator == 'LIKE'){
                        $value = "%" . trim($value) . "%"; 
                        $query->where(trim($field), $operator, $value);

                    }else if($operator == 'IN'){
                        $value = explode(',', str_replace(['(', ')'], '', $value));
                        $query->whereIn(trim($field), $value);
                    }else{
                        $query->where(trim($field), $operator,  trim($value));
                    }
                    return;
                }
            }
            // Fallback to simple equality
            if (strpos($condition, '=') !== false) {
                list($field, $value) = explode('=', $condition, 2);
                $query->where(trim($field), trim($value));
            }
        };
    }
}
