<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;
/**
 * @OA\Info(
 *      version="3.0",
 *      title="Api documentation",
 *      description="Api documentation",
 *      @OA\Contact(
 *          email="support@example.com"
 *      )
 * )
 *
 *
 * @OA\Tag(
 *     name="Auth",
 *     description="Methods for authentication"
 * )
 *
 * * @OA\Tag(
 *     name="Routing",
 *     description="Methods of routing"
 * )
 *
 *
 *
 *
 *  @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 *  )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
