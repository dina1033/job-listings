<?php

namespace App\Services\Filters;

class Tokenizer
{
    public static function tokenize(string $filterString): array
    {
        $parts = preg_split('/(AND|OR)/i', $filterString, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $tokens = [];
        $currentToken = '';
        $currentOperator = 'AND'; // Default to AND
        
        foreach ($parts as $part) {
            $part = trim($part);
            
            if (strtoupper($part) === 'AND' || strtoupper($part) === 'OR') {
                if (!empty($currentToken)) {
                    $tokens[] = [
                        'condition' => $currentToken,
                        'operator' => $currentOperator
                    ];
                    $currentToken = '';
                }
                $currentOperator = strtoupper($part);
            } else {
                $currentToken .= $part;
            }
        }
        if (!empty($currentToken)) {
            $tokens[] = [
                'condition' => $currentToken,
                'operator' => $currentOperator
            ];
        }
        
        return $tokens;
    }
}
