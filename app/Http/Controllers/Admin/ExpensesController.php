<?php

namespace App\Http\Controllers\Admin;

use App\Models\Expense;
use App\Services\FinancialDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpensesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'financial_expenses';
    }

    private function adminId(): ?int
    {
        return optional(Auth::guard('admin')->user())->id;
    }

    public function index(Request $request)
    {
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);
        $search = trim((string) $request->get('search', ''));

        $q = Expense::with('createdBy')
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month);
        if ($search !== '') {
            $q->where(function ($w) use ($search) {
                $w->where('statement', 'like', "%{$search}%")->orWhere('notes', 'like', "%{$search}%");
            });
        }
        $expenses = $q->orderByDesc('expense_date')->orderByDesc('id')->get();

        parent::$data['expenses']   = $expenses;
        parent::$data['year']       = $year;
        parent::$data['month']      = $month;
        parent::$data['search']     = $search;
        parent::$data['month_total'] = (float) $expenses->sum('amount');
        parent::$data['grand_total'] = (float) Expense::sum('amount');

        return view('admin.financial.expenses', parent::$data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_date' => 'required|date',
            'statement'    => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'notes'        => 'nullable|string|max:2000',
        ]);
        $data['created_by'] = $this->adminId();
        Expense::create($data);

        app(FinancialDashboardService::class)->forget();
        return back()->with('success', 'تم تسجيل المصروف بنجاح.');
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        $data = $request->validate([
            'expense_date' => 'required|date',
            'statement'    => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'notes'        => 'nullable|string|max:2000',
        ]);
        $expense->update($data);

        app(FinancialDashboardService::class)->forget();
        return back()->with('success', 'تم تحديث المصروف.');
    }

    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();
        app(FinancialDashboardService::class)->forget();
        return back()->with('success', 'تم حذف المصروف.');
    }
}
