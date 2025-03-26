<?php

namespace App\Services\Filters;

class LocationFilter
{
    public static function apply(string $condition)
    {
        return function($query) use ($condition) {
            if (preg_match('/locations\s*(HAS_ANY|IS_ANY|EXISTS|=)\s*\(?([^)]+)?\)?/i', $condition, $matches)){
                $cleanedCondition = trim($condition, " \t\n\r\0\x0B)");
                $operator = strtoupper($matches[1]); // Extract operator
                $values = isset($matches[2]) ? array_map('trim', explode(',', $matches[2])) : [];
                 // Define available strategies for handling conditions
                 $strategies = [
                    'HAS_ANY' => fn($q) => $q->whereHas('locations', fn($sq) => $sq->whereIn('city', $values)->orWhereIn('state', $values)->orWhereIn('country', $values)),
                    'IS_ANY'  => fn($q) => $q->whereHas('locations', fn($sq) => $sq->whereIn('city', $values)->orWhereIn('state', $values)->orWhereIn('country', $values)),
                    'EXISTS'  => fn($q) => $q->whereHas('locations'),
                    '='       => fn($q) => (count($values) === 1) 
                        ? $q->whereHas('locations', fn($sq) => $sq->where('city', $values[0])->orWhere('state', $values)->orWhere('country', $values)) 
                        : null
                ];
                // Execute the appropriate strategy
                if (isset($strategies[$operator])) {
                    $strategies[$operator]($query);
                } else {
                    \Log::warning('Unsupported Operator:', ['operator' => $operator]);
                }
            }
        };
    }
}
