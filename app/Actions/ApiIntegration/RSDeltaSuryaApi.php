<?php

namespace App\Actions\ApiIntegration;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RSDeltaSuryaApi
{
    public static function getApiToken()
    {
        return Cache::remember('rs_api_token', 86400, function () {
            $response = Http::post('https://recruitment.rsdeltasurya.com/api/v1/auth', [
                'email' => config('services.rsds.email'),
                'password' => config('services.rsds.password'),
            ]);

            return $response['access_token'];
        });
    }

    public static function getInsurancesData()
    {
        $token = self::getApiToken();

        $response = Http::withToken($token)
            ->get('https://recruitment.rsdeltasurya.com/api/v1/insurances');

        return $response['insurances'];
    }

    public static function getProceduresData()
    {
        $token = self::getApiToken();

        $response = Http::withToken($token)
            ->get('https://recruitment.rsdeltasurya.com/api/v1/procedures');

        return $response['procedures'];
    }

    public static function getProcedurePricesData(string $procedureId)
    {
        $token = self::getApiToken();

        $response = Http::withToken($token)
            ->get('https://recruitment.rsdeltasurya.com/api/v1/procedures/'.$procedureId.'/prices');

        return $response['prices'];
    }
}
