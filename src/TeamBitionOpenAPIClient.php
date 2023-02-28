<?php

namespace Stackboom\Teambition;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\LazyCollection;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class TeamBitionOpenAPIClient
{
    private $appId;
    private $appSecret;

    private bool $debug = false;

    private bool $processResponse = true;

    private \GuzzleHttp\Client $guzzle;

    public function __construct(string $appId, string $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;

        $this->guzzle = new \GuzzleHttp\Client([
            'base_uri' => env('TEAMBITION_API_HOST')
        ]);
    }

    public function instance(){
        return $this;
    }

    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
        return $this;
    }

    private function getBearerToken(): string
    {
        return (new JwtFacade())->issue(
            new Sha256(),
            InMemory::plainText($this->appSecret),
            fn(
                Builder $builder
            ): Builder => $builder
                ->withClaim('_appId', $this->appId)
                ->issuedAt(Carbon::now()->toDateTimeImmutable())
                ->expiresAt(Carbon::now()->addHour()->toDateTimeImmutable())
        )->toString();
    }

    public function setProcessResponse(bool $processResponse){
        $this->processResponse = $processResponse;
        return $this;
    }

    private function processResponse(Response $response,$resultKey='result'){
        $parsed = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        if($this->processResponse){
            return collect($parsed)->get($resultKey);
        }
        return $parsed;
    }

    public function post($uri,$data,$headers=[]):array {
        $response = $this->guzzle->post($this->debug?'https://httpbin.org/post':$uri,[
            'json'=>$data,
            'headers'=>array_merge([
                'Authorization'=>'Bearer '.$this->getBearerToken(),
            ],$headers)
        ]);
        return $this->processResponse($response);
    }

    public function get($uri,$query,$headers=[]):array {
        $response = $this->guzzle->get($this->debug?'https://httpbin.org/get':$uri,[
            'query'=>$query,
            'headers'=>array_merge([
                'Authorization'=>'Bearer '.$this->getBearerToken(),
            ],$headers)
        ]);
        return $this->processResponse($response);
    }

    public function getPaged($uri,$query,$headers=[],$perPage=10): LazyCollection {
        return LazyCollection::make(function () use ($perPage, $headers, $query, $uri) {
            $nextPageToken = null;
            do{
                $response = $this
                    ->setProcessResponse(false)
                    ->get($uri,array_merge($query,[
                        'pageSize'=>$perPage,
                        'pageToken'=>$nextPageToken,
                    ]),$headers);
                $nextPageToken = collect($response)->get('nextPageToken');
                yield from collect($response)->get('result');
            }while($nextPageToken !== null && $nextPageToken !== '');
        });
    }
}
