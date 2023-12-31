<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExepenseCollection;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Image;

class ExpensesController extends Controller
{
    public function index() {
        $now = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $stats = [
            'Today' => auth()->user()->expenses()->whereDate('expense_date', Carbon::now())->sum('amount'),
            'This Month' => auth()->user()->expenses()->whereBetween('expense_date', [$startOfMonth, $endOfMonth])->sum('amount'),
            'This Week' => auth()->user()->expenses()->whereBetween('expense_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount'),
            'Last 30 Days' => auth()->user()->expenses()->whereBetween('expense_date', [Carbon::now()->subMonth(), Carbon::now()])->sum('amount'),
            'Last 7 Days' => auth()->user()->expenses()->whereBetween('expense_date', [Carbon::now()->subWeek(), Carbon::now()])->sum('amount'),
        ];


        return Inertia::render('Expenses/Index', [
            'categories' => auth()->user()->categories()->withSum(['expenses' => fn($q) => $q->whereBetween('expense_date', [$startOfMonth, $endOfMonth])], 'amount')->latest()->get(),
            'expenses' => new ExepenseCollection(auth()->user()->expenses()->with('category')->latest('expense_date')->latest()->get()),
            'stats' => (object) $stats
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
        auth()->user()->expenses()->create($request->all());

        return to_route('expenses.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        abort_if($expense->user_id != auth()->id(), 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255']
        ]);

        $expense->update($data);

        return to_route('expenses.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        abort_if($expense->user_id != auth()->id(), 404);

        $expense->delete();

        return to_route('expenses.index');
    }


    public function upload(Request $request) {
        $request->validate([
            'file' => 'required|image'
        ]);

        $image = $request->file('file');
        $imageName = time().'.'.$image->extension();

        $destinationPathThumbnail = storage_path('/app/public/expenses');
        $img = Image::make($image->path());

        $path = $destinationPathThumbnail.'/'.$imageName;

        $img->resize(1100, null, function ($constraint) {
            $constraint->aspectRatio();
        })->save($path);

        return Redirect::back()->with([
            'data' => [
                'path' => 'expenses/'.$imageName,
                'url' => Storage::disk('public')->url('expenses/'.$imageName)
            ]
        ]);
    }
}
