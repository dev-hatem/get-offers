<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LinkMyDealsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkMyDealsController extends Controller
{
    public function __construct(private LinkMyDealsService $linkMyDealsService){}

    //http://127.0.0.1:8000/api/offers?format=csv&is_off=1&is_incremental=1&date=1448536485
    public function index(Request $request) :JsonResponse
    {

        $off_record   = $request->boolean('is_off');
        $last_extract = Carbon::parse($request->query('date', '2010-01-01'))->toDateTimeString();
        $incremental  = $request->boolean('is_incremental');
        $format       = $request->query('format', config('services.deals.default_format'));

        $resource = $this->linkMyDealsService->getOffers($off_record, $last_extract, $incremental, $format);

        return response()->json($resource, $resource['status']);
    }
}
