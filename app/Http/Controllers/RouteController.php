<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoordinatesRequest;
use App\Http\Requests\RouteRequest;
use App\Http\Resources\GeoPointsResource;
use App\Jobs\GetAddressByCoordinates;
use App\Models\Geoposition;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use OpenApi\Annotations as OA;


class RouteController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/route/save-coordinates",
     *     security={ {"bearerAuth": {}} },
     *     summary="Method for adding user's coordinates",
     *     tags={"Routing"},
     *     @OA\RequestBody(
     *         description="Save user's coordinates",
     *         @OA\JsonContent(
     *              required={"latitude", "longitude"},
     *              @OA\Property (property="latitude", type="string", example="51.0148"),
     *              @OA\Property (property="longitude", type="string", example="53.8152")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User saved coordinates successfully",
     *         @OA\JsonContent(
     *              @OA\Property (property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response="503",
     *         description="Service Unavailable",
     *     ),
     *     @OA\Response(response="403", ref="#/components/responses/403"),
     *     @OA\Response(response="422", ref="#/components/responses/422")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/route",
     *     tags={"Routing"},
     *     security={ {"bearerAuth": {}} },
     *     summary="Method for getting user's route by timeline",
     *     @OA\Parameter (
     *         parameter="timeFrom",
     *         name="timeFrom",
     *         in="query",
     *         required=true,
     *         description="Time from which to request user's geopoints",
     *         @OA\Schema (
     *             type="string",
     *             example="2023-09-22 19:59:50"
     *         )
     *     ),
     *     @OA\Parameter (
     *         parameter="timeTo",
     *         name="timeTo",
     *         in="query",
     *         required=true,
     *         description="Time until which to request user's geopoints",
     *         @OA\Schema (
     *             type="string",
     *             example="2023-09-22 19:59:50"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Array of user's geopoints and addresses with pagination",
     *         @OA\JsonContent(
     *              @OA\Property (property="data", type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property (property="point", type="object",
     *                          @OA\Property (property="latitude", type="string", example="56.847484"),
     *                          @OA\Property (property="longitude", type="string", example="53.202720")
     *                      ),
     *                      @OA\Property (
     *                          property="address",
     *                          type="string",
     *                          example="Россия, Удмуртская Республика, Ижевск, улица Карла Маркса, 191"
     *                      )
     *                  )
     *              )
     *         ),
     *     ),
     *     @OA\Response(response="403", ref="#/components/responses/403"),
     *     @OA\Response(response="422", ref="#/components/responses/422"),
     * )
     */
    public function getRoute(RouteRequest $request)
    {
        $request->validated();
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;

        /** @var Geoposition $geoPositions */
        $geoPositions = Geoposition::where('created_at', '>', $request->timeFrom)
            ->where('created_at', '<', $request->timeTo)
            ->where('user_id', '=', $user->id)->paginate(1);

        return GeoPointsResource::collection($geoPositions);
    }
}
