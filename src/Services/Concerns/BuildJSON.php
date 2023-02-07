<?php

namespace TomatoPHP\TomatoFigma\Services\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use TomatoPHP\TomatoFigma\Services\Figma;
use TomatoPHP\TomatoFigma\Services\Helpers\Color;
use stdClass;

class BuildJSON
{
    public Collection $json;
    public Figma $figma;

    public function __construct()
    {
        $this->json = collect();
        $this->figma = new Figma();
    }

    public static function make(object $children,string $image, string $key,bool $online): stdClass
    {
        return (new static)->get($children,$image, $key, $online);
    }

    public function get(object $children, string $image, string $key, bool $online): stdClass
    {
        if($online){
            $this->json->put('document',$this->load($children, $key));
            $this->json->put('screenshot',$image);
            $this->build($children->children,$key, $children->id);
            $this->save();
        }

        return json_decode(File::get(base_path('app.json')));
    }

    public function load(object $children,string $key, $parent=null, $mainPOS=null): Collection
    {
        $response = collect([]);

        $response->put('id', $children->id);
        $response->put('name', $children->name);
        $response->put('type', $children->type);
        if($children->name === "img"){
            foreach($this->figma->exportElement($key,$children->id)->images as $imageItem){
                $response->put('src',$imageItem);
            }
        }
        $response->put('width', $children->absoluteBoundingBox->width);
        $response->put('height', $children->absoluteBoundingBox->height);
        $response->put('parent_id', $parent);
        $backgrounds = [];
        foreach($children->fills as $fill){
            if(isset($fill->color)){
                $backgrounds[] = [
                    "type" => $fill->type,
                    "rgba" => Color::make($fill->color)->rgba(),
                    "hex" => Color::make($fill->color)->hex(),
                ];
            }
        }
        $response->put('backgrounds', $backgrounds);

        $effects = [];
        foreach($children->effects as $effect){
            if(isset($effect->color)){
                $effects[] = [
                    "type" => $effect->type,
                    "rgba" => Color::make($effect->color)->rgba(),
                    "hex" => Color::make($effect->color)->hex(),
                    "x" => $effect->offset->x,
                    "y" => $effect->offset->y,
                    "blur" => $effect->radius
                ];
            }
        }
        $response->put('effects', $effects);

        $stocks = [];
        foreach($children->strokes as $stroke){
            if(isset($stroke->color)){
                $stocks[] = [
                    "type" => $stroke->type,
                    "rgba" => Color::make($stroke->color)->rgba(),
                    "hex" => Color::make($stroke->color)->hex(),
                    "size" => $children->strokeWeight
                ];
            }
        }
        $response->put('stocks', $stocks);

        if($children->type === 'TEXT'){
            $response->put('text', $children->characters);
            $response->put('font-family', $children->style->fontFamily);
            $response->put('font-size', $children->style->fontSize);
            $response->put('font-weight', $children->style->fontWeight);
            $response->put('line-height', $children->style->lineHeightPx);
            $response->put('letter-spacing', $children->style->letterSpacing);
        }

        if(isset($children->primaryAxisAlignItems)){
            $response->put('flex', $children->primaryAxisAlignItems);
        }
        if(isset($children->layoutMode)){
            $response->put('align', $children->layoutMode);
        }
        if(isset($children->paddingLeft)){
            $response->put('paddingLeft',$children->paddingLeft);
        }
        if(isset($children->paddingRight)){
            $response->put('paddingRight', $children->paddingRight);
        }
        if(isset($children->paddingTop)){
            $response->put('paddingTop', $children->paddingTop);
        }
        if(isset($children->paddingBottom)){
            $response->put('paddingBottom', $children->paddingBottom);
        }
        if(isset($children->itemSpacing)){
            $response->put('itemSpacing', $children->itemSpacing);
        }

        $corners = [];
        if(isset($children->rectangleCornerRadii ) && count($children->rectangleCornerRadii ) > 3){
            $corners[] = [
                "topLeft" => $children->rectangleCornerRadii[0],
                "topRight" => $children->rectangleCornerRadii[1],
                "bottomRight" => $children->rectangleCornerRadii[2],
                "bottomLeft" => $children->rectangleCornerRadii[3],
            ];
        }
        else if(isset($children->cornerRadius) && $children->cornerRadius){
            $corners[] = [
                "topLeft" => $children->cornerRadius,
                "topRight" => $children->cornerRadius,
                "bottomRight" => $children->cornerRadius,
                "bottomLeft" => $children->cornerRadius,
            ];
        }

        $response->put('corners', $corners);

        $response->put('parent-pos', $mainPOS);

        if($children->absoluteBoundingBox){
            if($mainPOS){
                $left= ($mainPOS['x'] - $children->absoluteBoundingBox->x) ? ($mainPOS['x'] - $children->absoluteBoundingBox->x)*-1 : 0;
                $top = ($mainPOS['y'] - $children->absoluteBoundingBox->y) ? ($mainPOS['y'] - $children->absoluteBoundingBox->y)*-1 : 0;
                $center_h = (int)(($mainPOS['width']-$children->absoluteBoundingBox->width)/2) === (int)$left;
                $center_v = (int)(($mainPOS['height']-$children->absoluteBoundingBox->height)/2) === (int)$top;


                $response->put('Hcenter',$center_h);
                $response->put('Vcenter',$center_v);
                $response->put('left',$left);
                $response->put('top', $top);
            }
            else {
                $response->put('left',($children->absoluteBoundingBox->x*-1));
                $response->put('top', ($children->absoluteBoundingBox->y*-1));
            }
        }

        $components = collect([]);
        if(isset($children->children) && count($children->children)){
            foreach($children->children as $item){
                if($item->name === 'placeholder'){
                    $children->placeholder = $item;
                    $response->put('placeholder', $this->load($item, $key, $children->id));
                }
                else if($item->name === 'icons'){
                    $item->name = "img";
                    $response->put('icon', $this->load($item, $key, $children->id));
                }
                else if($item->name === 'style'){
                    $response->put('style', $this->load($item, $key, $children->id));
                }
                else {
                    $mainPOSSet = [
                        "width" => $children->absoluteBoundingBox->width,
                        "height" => $children->absoluteBoundingBox->height,
                        "x" => $children->absoluteBoundingBox->x,
                        "y" => $children->absoluteBoundingBox->y
                    ];
                    $components->push($this->load($item, $key, $children->id, $mainPOSSet));
                }
            }
        }

        $response->put('components' ,$components->toArray());

        return $response;
    }

    public function build(array $children, string $key,string $id, bool $sub=false, ?string $subKey=null, $mainPOS=null):void
    {
        foreach($children as $item){
            $this->load($item, $key, $id, $mainPOS);
            if(isset($item->children) && count($item->children)){
                $mainPOS = [
                    "name" =>$item->name,
                    "width" => $item->absoluteBoundingBox->width,
                    "height" => $item->absoluteBoundingBox->height,
                    "x" => $item->absoluteBoundingBox->x,
                    "y" => $item->absoluteBoundingBox->y
                ];
                $this->build($item->children, $key,$id, true, $item->id, $mainPOS);
            }
        }
    }

    public function toJson(): string
    {
        return $this->json->toJson();
    }

    public function save(): void
    {
        File::put(base_path('app.json'), $this->toJson());
    }
}
