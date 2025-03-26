<?php

namespace App\Services;

use App\Models\Job;
use App\Services\Filters\Tokenizer;
use App\Services\Filters\ConditionParser;
use Illuminate\Database\Eloquent\Builder;

class JobFilterService
{
    protected $query;
    protected $filters;

    public function apply(array $filters)
    {
        $this->query = Job::query();
        $this->filters = $filters;

        $filterString = $filters['filter'] ?? '';

        if (empty($filterString)) {
            return $this->query;
        }

        try {
            $tokens = Tokenizer::tokenize($filterString);

            if (empty($tokens)) {
                return $this->query;
            }

            $parsedCondition = ConditionParser::parse($tokens);

            if ($parsedCondition) {
                $parsedCondition($this->query);
            }
        } catch (\Exception $e) {
            \Log::error('Filter parsing error: ' . $e->getMessage());
            return $this->query;
        }

        $this->query->with(['languages', 'locations', 'categories', 'attributeValues.attribute']);

        return $this->query;
    }
}
