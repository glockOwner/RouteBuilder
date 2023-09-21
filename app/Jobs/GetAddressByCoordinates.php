<?php

namespace App\Jobs;

use App\Models\Geoposition;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\RequestOptions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;
use function Symfony\Component\Translation\t;

class GetAddressByCoordinates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const YANDEX_GEOCODING_API_URI = 'https://geocode-maps.yandex.ru/1.x';

    /**
     * @var string $latitude
     */
    private $latitude;

    /**
     * @var string $longitude
     */
    private $longitude;

    /**
     * @var User $user
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $latitude, string $longitude, string $token)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $token = PersonalAccessToken::findToken($token);
        $this->user = $token->tokenable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = $this->getAddress($this->latitude, $this->longitude);

        Geoposition::create([
            'user_id' => $this->user->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $response
        ]);
    }

    private function getAddress(string $latitude, string $longitude)
    {
        $coordinates = $latitude . ', ' . $longitude;
        $options = [
            'apikey' => env('YANDEX_GEOCODING_API_KEY'),
            'geocode' => $coordinates,
            'sco' => 'latlong',
            'format' => 'json'
        ];
        $response = Http::get(static::YANDEX_GEOCODING_API_URI, $options)->json();

        return $response['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['text'];
    }
}
