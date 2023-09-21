<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoordinatesRequest;
use App\Http\Requests\RouteRequest;
use App\Jobs\GetAddressByCoordinates;
use App\Models\Geoposition;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class RouteController extends Controller
{
    public function saveCoordinates(CoordinatesRequest $request)
    {
        $request->validated();

        try {
            GetAddressByCoordinates::dispatch($request->latitude, $request->longitude, $request->bearerToken());
        } catch (\Exception $e) {
            abort(503, 'Service Unavailable');
        }

        return ['success' => true];
    }

    public function getRoute(RouteRequest $request)
    {
        $request->validated();
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;

        /** @var Geoposition $geoPositions */
        $geoPositions = Geoposition::where('created_at', '>', $request->timeFrom)
            ->where('created_at', '<', $request->timeTo)
            ->where('user_id', '=', $user->id);

        return $geoPositions->jsonPaginate();
    }
}
