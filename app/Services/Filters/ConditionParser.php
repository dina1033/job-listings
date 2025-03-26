<?php

namespace App\Services\Filters;

use App\Services\Filters\AttributeFilter;
use App\Services\Filters\LanguageFilter;
use App\Services\Filters\LocationFilter;

class ConditionParser
{
    public static function parse(array $tokens)
    {
        return function ($query) use ($tokens) {
            $firstCondition = true;

            foreach ($tokens as $token) {
                $condition = $token['condition'];
                $operator = $token['operator'];

                $conditionClosure = self::parseCondition($condition);

                if ($firstCondition) {
                    $conditionClosure($query);
                    $firstCondition = false;
                } else {
                    if ($operator === 'OR') {
                        $query->orWhere(fn($q) => $conditionClosure($q));
                    } else {
                        $query->where(fn($q) => $conditionClosure($q));
                    }
                }
            }
        };
    }

    protected static function parseCondition(string $condition)
    {
        if (strpos($condition, 'attribute:') !== false) {
            return AttributeFilter::apply($condition);
        } elseif (strpos($condition, 'languages') !== false) {
            return LanguageFilter::apply($condition);
        } elseif (strpos($condition, 'locations') !== false) {
            return LocationFilter::apply($condition);
        } else {
            return BaseFilter::apply($condition);
        }
    }
}
