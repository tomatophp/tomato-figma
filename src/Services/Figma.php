<?php

namespace TomatoPHP\TomatoFigma\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

/**
 *
 */
class Figma
{
    /**
     * @var string|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed|null
     */
    private string|null $figmaToken;
    /**
     * @var string|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed|null
     */
    private string|null $token;

    /**
     * @param string $endpoint
     * @param string $figmaEndPoint
     */
    public function __construct(
        private string $endpoint = "https://api.opendesign.dev/",
        private string $figmaEndPoint= 'https://api.figma.com/'
    )
    {
        $this->figmaToken = config('tomato-figma.figma.token');
        $this->token = config('tomato-figma.open_design.token');
    }

    /**
     * @return array|mixed
     */
    public function checkToken(): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->get($this->endpoint.'token');
        return $response->json();
    }

    /**
     * @param $key
     * @return array|mixed
     */
    public function importFigmaFromURL($key): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->post($this->endpoint.'designs/figma-link', [
            "figma_filekey" => $key
        ]);

        return $response->json();
    }

    /**
     * @return array|mixed
     */
    public function designList(): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->get($this->endpoint.'designs');
        return $response->json();
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function designVersions($id): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->get($this->endpoint.'designs/' . $id. '/versions');
        return $response->json();
    }

    /**
     * @param $id
     * @param $version
     * @return array|mixed
     */
    public function designVersionInfo($id, $version): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->get($this->endpoint.'designs/' . $id. '/versions/' . $version);
        return $response->json();
    }

    /**
     * @param $id
     * @param $version
     * @return array|mixed
     */
    public function designVersionSummary($id, $version): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->get($this->endpoint.'designs/' . $id. '/versions/' . $version .'/summary');
        return $response->json();
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function designById($id): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->get($this->endpoint.'designs/' .$id);
        return $response->json();
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function designSummary($id): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->get($this->endpoint.'designs/' .$id . '/summary');
        return $response->json();
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function designPages($id): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->get($this->endpoint.'designs/' .$id . '/pages');
        return $response->json();
    }

    /**
     * @param $id
     * @param $page
     * @return array|mixed
     */
    public function designPageById($id, $page): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->get($this->endpoint.'designs/' .$id . '/pages/' . $page);
        return $response->json();
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function designArtboards($id): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->get($this->endpoint.'designs/' .$id . '/artboards');
        return $response->json();
    }

    /**
     * @param $id
     * @param $artboard
     * @return mixed
     */
    public function designArtboardContent($id, $artboard): mixed
    {
        $response = Http::withHeaders([
            "Authorization" => 'Bearer '.$this->token
        ])->get($this->endpoint.'designs/' .$id . '/artboards/' . $artboard . '/content');
        return $response->json();
    }

    /**
     * @param string $key
     * @param string $ids
     * @return mixed
     */
    public function getElementById(string $key, string $ids): mixed
    {
        $response = Http::withHeaders([
            "X-Figma-Token"=> $this->figmaToken
        ])->get($this->figmaEndPoint.'v1/files/' .$key . '/nodes?ids=' . $ids);
        return json_decode($response->body());
    }

    /**
     * @param string $key
     * @param string $ids
     * @return mixed
     */
    public function exportElement(string $key, string $ids): mixed
    {
        $response = Http::withHeaders([
            "X-Figma-Token"=> $this->figmaToken
        ])->get($this->figmaEndPoint.'v1/images/' .$key . '/?ids=' . $ids."&format=png");
        return json_decode($response->body());
    }
}
