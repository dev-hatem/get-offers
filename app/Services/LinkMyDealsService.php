<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

/**
 * status [new - updated - suspended]
 */


class LinkMyDealsService
{
    public function getOffers($off_record, $last_extract, $incremental, $format) :array
    {
        try {
            if (method_exists($this, $format)){
                $response =  Http::baseUrl(config('services.deals.url'))->get('getOffers', [
                    'API_KEY'      => config('services.deals.key'),
                    'format'       => $format,
                    'off_record'   => $off_record,
                    'last_extract' => $last_extract,
                    'incremental'  => $incremental,
                ]);

                return  [
                    'data'    => call_user_func([$this, $format], $response),
                    'message' => 'success',
                    'status'  => 200
                ];
            }
            throw new Exception(sprintf("undefined format %s", $format));
        }catch (Exception $exception){
            return  [
                'data'    => null,
                'message' => 'fail',
                'error'   => $exception->getMessage(),
                'status'  => 500
            ];
        }
    }

    private function json($response)
    {
        return $response->json('offers');
    }


    private function csv($response)
    {
        $response = collect(explode("\n", $response->body()));

        $offers = [];
        $header   = explode(',', $response->first());

        if (count($header) === 1)
            return $header[0];

        $response->each(function ($row, $index) use ($header, &$offers){
            if ($index > 0){
                $offers[] = array_combine($header, explode(',', $row));
            }
        });

        return $offers;
    }
}
