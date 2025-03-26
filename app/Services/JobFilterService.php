<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class JobFilterService
{
    protected $query;
    protected $filters;

    /**
     * Apply filters to the job query
     * 
     * @param array $filters Filtering parameters
     * @return Builder Filtered query
     */
    public function apply(array $filters)
    {
        $this->query = Job::query();
        $this->filters = $filters;

        $filterString = $filters['filter'] ?? '';
        
        try {
            if (empty($filterString)) {
                return $this->query;
            }

            // Tokenize with both AND and OR
            $tokens = $this->tokenize($filterString);
            
            if (empty($tokens)) {
                return $this->query;
            }

            $parsedCondition = $this->parseConditions($tokens);

            if ($parsedCondition) {
                $parsedCondition($this->query);
            }
        } catch (\Exception $e) {
            \Log::error('Filter parsing error: ' . $e->getMessage());
            \Log::error('Filter string: ' . $filterString);
            return $this->query;
        }

        $this->query->with([
            'languages', 
            'locations', 
            'categories', 
            'attributeValues.attribute'
        ]);

        return $this->query;
    }

    /**
     * Tokenize the filter string with enhanced parsing
     * 
     * @param string $filterString Filter string to tokenize
     * @return array Tokens
     */
    protected function tokenize(string $filterString)
    {
        // Split by AND/OR but keep the delimiters
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
        
        // Add the last token
        if (!empty($currentToken)) {
            $tokens[] = [
                'condition' => $currentToken,
                'operator' => $currentOperator
            ];
        }
        
        return $tokens;
    }

    /**
     * Parse conditions with fallback mechanism
     * 
     * @param array $tokens Tokens to parse
     * @return \Closure|null Parsed condition
     */
    protected function parseConditions(array $tokens)
    {
        if (empty($tokens)) {
            return function($query) {};
        }

        try {
            return $this->parseComplexConditions($tokens);
        } catch (\Exception $e) {
            return $this->parseFallbackConditions($tokens);
        }
    }

    /**
     * Parse complex nested conditions
     * 
     * @param array $tokens Tokens to parse
     * @return \Closure Parsed complex condition
     */
    protected function parseComplexConditions(array $tokens)
    {
        return function($query) use ($tokens) {
            $firstCondition = true;
            
            foreach ($tokens as $token) {
                $condition = $token['condition'];
                $operator = $token['operator'];
                
                $conditionClosure = $this->parseSingleCondition($condition);
                
                if ($firstCondition) {
                    // For the first condition, always use where
                    $conditionClosure($query);
                    $firstCondition = false;
                } else {
                    // For subsequent conditions, use the specified operator
                    if ($operator === 'OR') {
                        $query->orWhere(function($q) use ($conditionClosure) {
                            $conditionClosure($q);
                        });
                    } else {
                        $query->where(function($q) use ($conditionClosure) {
                            $conditionClosure($q);
                        });
                    }
                }
            }
        };
    }

    /**
     * Fallback simple parsing for basic conditions
     * 
     * @param array $tokens Tokens to parse
     * @return \Closure Simple parsed condition
     */
    protected function parseSingleCondition(string $condition)
    {
        if (strpos($condition, 'attribute:') !== false) {
            return $this->parseAttributeCondition($condition);
        } elseif (strpos($condition, 'languages') !== false) {
            return $this->parseLanguageCondition($condition);
        } elseif (strpos($condition, 'locations') !== false) {
            return $this->parseLocationCondition($condition);
        } else {
            return $this->parseStandardCondition($condition);
        }
    }

    protected function parseFallbackConditions(array $tokens)
    {
        return function($query) use ($tokens) {
            $firstCondition = true;
            
            foreach ($tokens as $token) {
                $condition = $token['condition'];
                $operator = $token['operator'];
                
                if ($firstCondition) {
                    $this->applySimpleFallbackFilter($query, $condition);
                    $firstCondition = false;
                } else {
                    if ($operator === 'OR') {
                        $query->orWhere(function($q) use ($condition) {
                            $this->applySimpleFallbackFilter($q, $condition);
                        });
                    } else {
                        $query->where(function($q) use ($condition) {
                            $this->applySimpleFallbackFilter($q, $condition);
                        });
                    }
                }
            }
        };
    }

    /**
     * Apply simple fallback filter
     * 
     * @param Builder $query Query builder
     * @param string $condition Condition string
     */
    protected function applySimpleFallbackFilter(Builder $query, string $condition)
    {
        // Simple key-value parsing
        if (strpos($condition, '=') !== false) {
            list($field, $value) = explode('=', $condition, 2);
            $this->query->where(trim($field), trim($value));
        }
    }

    /**
     * Parse attribute condition
     * 
     * @param string $condition Attribute condition
     * @return \Closure Attribute filtering closure
     */
    protected function parseAttributeCondition(string $condition)
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
                        $q->whereIN('value', $value);
                    }else{
                        $q->where('value',$operator, $value);
                    }
                });
            }
        };
    }

    /**
     * Parse language condition
     * 
     * @param string $condition Language condition
     * @return \Closure Language filtering closure
     */
    protected function parseLanguageCondition(string $condition)
    {
        return function ($query) use ($condition) {
            // 🛠️ Clean the condition before processing
            $cleanedCondition = trim($condition, " \t\n\r\0\x0B)"); // Trim spaces & extra `)`
    
            // Updated regex pattern with improved handling of parentheses
            if (preg_match('/languages\s*(HAS_ANY|IS_ANY|EXISTS|=)\s*\(?([^)]+)?\)?/i', $cleanedCondition, $matches)) {
                \Log::info('Regex Matched:', ['matches' => $matches]);
    
                $operator = strtoupper($matches[1]); // Extract operator
                $values = isset($matches[2]) ? array_map('trim', explode(',', $matches[2])) : [];
 
                // Define available strategies for handling conditions
                $strategies = [
                    'HAS_ANY' => fn($q) => $q->whereHas('languages', fn($sq) => $sq->whereIn('name', $values)),
                    'IS_ANY'  => fn($q) => $q->whereHas('languages', fn($sq) => $sq->whereIn('name', $values)),
                    'EXISTS'  => fn($q) => $q->whereHas('languages'),
                    '='       => fn($q) => (count($values) === 1) 
                        ? $q->whereHas('languages', fn($sq) => $sq->where('name', $values[0])) 
                        : null
                ];
    
                // Execute the appropriate strategy
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
    
    
    

    /**
     * Parse location condition
     * 
     * @param string $condition Location condition
     * @return \Closure Location filtering closure
     */
    protected function parseLocationCondition(string $condition)
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

    /**
     * Parse standard condition
     * 
     * @param string $condition Standard condition
     * @return \Closure Standard filtering closure
     */
    protected function parseStandardCondition(string $condition)
    {
        return function($query) use ($condition) {
            // Handle different comparison operators
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