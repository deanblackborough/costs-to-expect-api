<?php

namespace App\Http\Controllers;

use App\Validators\Request\Parameters;
use App\Models\RequestLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryRequestController extends Controller
{
    private $collection_parameters;

    /**
     * Return a summary of the access log, monthly
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function monthlyAccessLog(Request $request): JsonResponse
    {
        $this->collection_parameters = Parameters::fetch(['source']);

        $monthly_summary = (new RequestLog())->monthlyRequests($this->collection_parameters);

        $summary = [];
        foreach ($monthly_summary as $month) {
            $summary[$month['year']][] = ['month' => $month['month'], 'requests' => $month['requests']];
        }

        return response()->json(
            $summary,
            200
        );
    }

    /**
     * Generate the OPTIONS request for summary of the access log
     *
     * @param Request $request
     */
    public function optionsMonthlyAccessLog(Request $request)
    {
        return $this->generateOptionsForIndex(
            [
                'description_localisation_string' => 'route-descriptions.summary_GET_request_access-log_monthly',
                'parameters_config_string' => 'api.request.parameters.collection',
                'conditionals_config' => [],
                'sortable_config' => null,
                'enable_pagination' => false,
                'authentication_required' => false
            ]
        );
    }
}
