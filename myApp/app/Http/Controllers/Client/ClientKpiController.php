<?php

namespace App\Http\Controllers\Client;

use App\Classes\KpiRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\KPI\BulkCreateKpiRequest;

/**
 * @group Client API - KPI
 *
 * Client APIs for magaging KPI data.
 *
 * KPI provide a record of a user's performance over time. Each KPI record is a
 * simple timestamped value. KPI are highly configurable, allowing the system to
 * work with whatever metric the client requires. For example, a KPI record
 * could hold a percentage, a true/false value, or just a simple numeric value.
 *
 * A user’s KPI records can be aggregated over a specific time period to provide
 * an overall score for that stretch of time.
 */
class ClientKpiController extends Controller
{
    /**
     * Add KPI Data
     *
     * Creates one or more KPI records using the values provided in an array.
     *
     * <b>Note:</b> If the `AUTO_CREATE_USERS_ON_KPI_SUBMIT` feature is enabled,
     * any KPI `email` or `external_id` values for which a user cannot be found
     * will result in a user being created automatically.
     *
     * @bodyParam kpi[0][user_id] int required Identifies which user to record this KPI against. It is required when both email and external id are absent. Example: 1
     * @bodyParam kpi[0][email] string required Max: 191. Identifies which user to record this KPI against. It is required when both user id and external id are absent. Must be a valid email address. Example: gevokihe@mail-guru.net
     * @bodyParam kpi[0][external_id] string required Max: 255. Identifies which user to record this KPI against. It is required when both email and user id are absent. Example: EXT0001
     * @bodyParam kpi[0][value] float required The value of this KPI record. Example: 0.53
     * @bodyParam kpi[0][weight] float The weight given to this KPI record's value. This is used when calculating weighted averages. Example: 1.42
     * @bodyParam kpi[0][timestamp] datetime The date and time recorded against this KPI. Formatted as yyyy-mm-dd hh:mm (24-hour clock) Example: 2019-03-22 23:05
     * @bodyParam kpi[0][metadata] json Metadata for the KPI record. Example: [{"title":"john"}, {"url" : "https://temp-mail.org/en/"}]
     *
     * @responseFile 200 resources/responses/Client/create.json
     * @responseFile 422 resources/responses/Client/create-422.json
     */
    public function addKPI(BulkCreateKpiRequest $req)
    {
        $kpi = KpiRepository::addKPI($req->validated()['kpi']);
        return response()->success(__('score')['kpi_add_success'], $kpi);
    }
}
