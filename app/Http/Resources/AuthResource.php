<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function __construct($resource, $accessToken, $refreshToken)
    {
        parent::__construct($resource);
        $this->resource = $resource;

        $this->accessToken = $accessToken->plainTextToken;
        $this->accessTokenExpiredAt = ($accessToken->accessToken->getAttributes())['expired_at'];
        $this->refreshToken = $refreshToken->plainTextToken;
        $this->refreshTokenExpiredAt = ($refreshToken->accessToken->getAttributes())['expired_at'];
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'email' => $this->email,
            'access_token' => [
                'token' => $this->accessToken,
                'expired_at' => $this->accessTokenExpiredAt,
            ],
            'refresh_token' => [
                'token' => $this->refreshToken,
                'expired_at' => $this->refreshTokenExpiredAt,
            ],
        ];
    }
}
