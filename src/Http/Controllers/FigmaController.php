<?php

namespace TomatoPHP\TomatoFigma\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use ProtoneMedia\Splade\Facades\Toast;
use TomatoPHP\TomatoFigma\Services\Concerns\BuildJSON;
use TomatoPHP\TomatoFigma\Services\Concerns\Key;
use TomatoPHP\TomatoFigma\Services\Concerns\Response;
use TomatoPHP\TomatoFigma\Services\Figma;
use TomatoPHP\TomatoFigma\Services\Generator\HTML;
use TomatoPHP\TomatoFigma\Services\Generator\Tailwind;

class FigmaController extends Controller
{
    public function index()
    {
        return view('tomato-figma::index');
    }


    /**
     * @param Request $request
     * @return \Exception|Application|Factory|View|RedirectResponse
     */
    public function files(Request $request): \Exception|Application|Factory|View|RedirectResponse
    {
        $request->validate([
            "url" => "required|string",
            "dir" => "required|boolean",
            "fonts" => "nullable|array",
            "type" => "required|string|in:html,tailwind",
        ]);

        $explodeURL = Key::make($request->get('url'));
        $response = Response::make($explodeURL->key, $explodeURL->node);
        if(!$response){
            Toast::danger('Invalid Token')->autoDismiss(2);
            return back();
        }
        else {
            $json = BuildJSON::make($response->children,$response->image, $explodeURL->key, config('tomato-figma.online'));
            $fonts = array_values(collect($request->get('fonts'))->pluck('font')->toArray());
            if($request->get('type') === 'html'){
                HTML::make($json, $explodeURL->key, $fonts ?: [], $request->get('dir')? 'rtl' : 'ltr');

            }
            else if($request->get('type') === 'tailwind') {
                Tailwind::make($json, $explodeURL->key, $fonts ?: [], $request->get('dir')? 'rtl' : 'ltr');
            }

            Toast::success('Your File Has Been Converted Successfully')->autoDismiss(2);

            return view('tomato-figma::index', [
                "body"=> $json,
                "url" => url('figma/' . $explodeURL->key .'.html')
            ]);
        }
    }
}
