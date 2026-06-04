<?php

namespace App\Http\Controllers\Admin;

use App\Services\FinancialDashboardService;

class FinancialDashboardController extends AdminController
{
    protected FinancialDashboardService $service;

    public function __construct(FinancialDashboardService $service)
    {
        parent::__construct();
        parent::$data['active_menu'] = 'financial_center';
        $this->service = $service;
    }

    /** The Financial Center page shell (cards/charts are filled via AJAX). */
    public function getIndex()
    {
        return view('admin.dashboard.financial', parent::$data);
    }

    /** Live JSON payload consumed on load and on the 60s auto-refresh. */
    public function getData()
    {
        return response()->json($this->service->payload());
    }
}
