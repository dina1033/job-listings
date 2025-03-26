<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JobFilterService;

class JobController extends Controller
{
    protected $filterService;

    public function __construct(JobFilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    public function index(Request $request)
    {
        $filters = $request->only('filter');
        $query = $this->filterService->apply($filters);
        $perPage = $request->input('per_page', 15);
        $jobs = $query->published()
            ->paginate($perPage);
            
        return response()->json($jobs);
    }
}
