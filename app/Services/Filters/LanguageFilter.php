<?php

namespace App\Services\Filters;

class LanguageFilter
{
    public static function apply(string $condition)
    {
        return function ($query) use ($condition) {
            $cleanedCondition = trim($condition, " \t\n\r\0\x0B)"); // Trim spaces & extra )

            if (preg_match('/languages\s*(HAS_ANY|IS_ANY|EXISTS|=)\s*\(?([^)]+)?\)?/i', $cleanedCondition, $matches)) {
                \Log::info('Regex Matched:', ['matches' => $matches]);
    
                $operator = strtoupper($matches[1]); // Extract operator
                $values = isset($matches[2]) ? array_map('trim', explode(',', $matches[2])) : [];
                $strategies = [
                    'HAS_ANY' => fn($q) => $q->whereHas('languages', fn($sq) => $sq->whereIn('name', $values)),
                    'IS_ANY'  => fn($q) => $q->whereHas('languages', fn($sq) => $sq->whereIn('name', $values)),
                    'EXISTS'  => fn($q) => $q->whereHas('languages'),
                    '='       => fn($q) => (count($values) === 1) 
                        ? $q->whereHas('languages', fn($sq) => $sq->where('name', $values[0])) 
                        : null
                ];
                    if (isset($strategies[$operator])) {
                    $strategies[$operator]($query);
                } else {
                    \Log::warning('Unsupported Operator:', ['operator' => $operator]);
                }
            } else {
                \Log::error('Regex did NOT match:', ['condition' => $cleanedCondition]);
            }
        };
    }
}
