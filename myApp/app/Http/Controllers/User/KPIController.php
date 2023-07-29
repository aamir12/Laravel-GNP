<?php

namespace App\Http\Controllers\User;

use App\Classes\Validators\KpiValidator;
use App\Classes\KpiRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group User API - KPI
 *
 * User APIs for managing KPI data.
 */
class KPIController extends Controller
{
    /**
     * Add KPI Data
     *
     * Creates one or more KPI records using the values provided in an array.
     *
     * @bodyParam kpi[0][value] float required The value of this KPI record. Example: 0.53
     * @bodyParam kpi[0][weight] The weight given to this KPI record's value. This is used when calculating weighted averages. Example: 1.42
     * @bodyParam kpi[0][timestamp] datetime The date and time recorded against this KPI. Formatted as yyyy-mm-dd hh:mm (24-hour clock) Example: 2019-03-22 23:05
     *
     * @responseFile 200 resources/responses/User/KPI/create.json
     * @responseFile 422 resources/responses/User/KPI/create-422.json
     */
    public function addKPI(Request $req)
    {
        $req->validate([
            'kpi' => 'required|Array',
            'kpi.*.value' => KpiValidator::kpiValueValidationRules(),
            'kpi.*.weight' => 'numeric',
            'kpi.*.timestamp' => 'date|date_multi_format:Y-m-d H:i,Y-m-d H:i:s.v'
        ]);

        $kpiData = collect($req->kpi)->map(function ($kpi) {
            $kpi['user_id'] = Auth::id();
            return $kpi;
        });

        return response()->success(__('score')['kpi_add_success'], KpiRepository::addKPI($kpiData->all()));
    }

    /**
     * List KPI Data
     *
     * Lists the currently authenticated user's KPI data.
     */
    public function listKPI()
    {
        $paginatedScores = KpiRepository::listKpiDataForUser(Auth::id(), true);
        return response()->success(__('score')['kpi_list_success'], $paginatedScores, true);
    }
}
