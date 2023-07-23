<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $categories = auth()->user()->categories()->withSum(['expenses' => fn($q) => $q->whereBetween('expense_date', [$startOfMonth, $endOfMonth])], 'amount')->latest()->get();

        return Inertia::render('Categories/Index', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'monthly_limit' => ['required', 'numeric']
        ]);

        auth()->user()->categories()->create($data);

        return to_route('categories.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        abort_if($category->user_id != auth()->id(), 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'monthly_limit' => ['required', 'numeric']
        ]);

        $category->update($data);

        return to_route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        abort_if($category->user_id != auth()->id(), 404);

        $category->delete();

        return to_route('categories.index');
    }
}
