<?php

namespace App\Services\Filters;

class AttributeFilter
{
    public static function apply(string $condition)
    {
        return function($query) use ($condition) {
            preg_match('/attribute:([^=<>!]+)\s*(=|>=|<=|>|<|LIKE|IN|NOT LIKE)\s*(.+)/i', $condition, $matches);
            
            if (count($matches) === 4) {
                $attributeName = $matches[1];
                $operator = $matches[2];
                $value = $matches[3];
                $query->whereHas('attributeValues.attribute', function ($q) use ($attributeName, $operator, $value) {
                    $q->where(function ($qe) use($attributeName){
                        $qe->where('name', $attributeName)
                        ->orWhere('type',$attributeName)
                        ->orWhere('options', $attributeName);
                    });
                    if($operator == 'IN'){
                        $value = explode(',', str_replace(['(', ')'], '', $value));
                        $q->whereIN('job_attribute_values.value', $value);
                    }else{
                        $q->where('job_attribute_values.value',$operator, $value);
                    }
                });
            }
        };
    }
}
