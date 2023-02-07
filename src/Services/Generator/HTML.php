<?php

namespace TomatoPHP\TomatoFigma\Services\Generator;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use TomatoPHP\TomatoFigma\Services\Helpers\CheckFolder;
use TomatoPHP\TomatoFigma\Services\Helpers\IDGenerator;
use TomatoPHP\TomatoFigma\Services\Helpers\Spacing;
use TomatoPHP\TomatoFigma\Services\Helpers\Tags;
use stdClass;

class HTML implements Generator
{
    use CheckFolder;
    use IDGenerator;
    use Spacing;

    private string $path;
    private stdClass $json;
    private Collection $content;
    private Collection $css;
    private Collection $body;

    public static function make(stdClass $json, string $key,array $fonts,string $dir): void
    {
        (new static)
            ->json($json)
            ->content(collect([]))
            ->css(collect([]))
            ->body(collect([]))
            ->path(public_path('figma'))
            ->generate($key,$fonts,$dir);
    }

    public function json(stdClass $json): static
    {
        $this->json = $json;
        return $this;
    }

    public function path(string $path): static
    {
        $this->path = $path;
        return $this;
    }

    public function content(Collection $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function css(Collection $css): static
    {
        $this->css = $css;
        return $this;
    }

    public function body(Collection $body): static
    {
        $this->body = $body;
        return $this;
    }

    public function generate(string $key,array $fonts,string $dir): void
    {
        $this->checkFolder($this->path);
        $this->buildBodyCss();
        $this->buildBodyHTML();
        $this->buildFullHTML($dir, $fonts, $key);
        $this->save($this->path, $key);
    }

    public function buildBodyCss(): void
    {
        $this->css->push('            body{margin: 0; padding: 0;}');
        $this->css->push('            #app{');
        $this->css->push('                background-color:' . $this->json->document->backgrounds[0]->rgba . ';');
        $this->css->push('                overflow:hidden;');
        if(isset($this->json->document->paddingLeft)){
            $this->css->push('                padding-left:'.$this->json->document->paddingLeft.'px;');
        }
        if(isset($this->json->document->paddingRight)){
            $this->css->push('                padding-right:'.$this->json->document->paddingRight.'px;');
        }
        if(isset($this->json->document->paddingTop)){
            $this->css->push('                padding-top:'.$this->json->document->paddingTop.'px;');
        }
        if(isset($this->json->document->paddingBottom)){
            $this->css->push('                padding-bottom:'.$this->json->document->paddingBottom.'px;');
        }
        $this->css->push('                width:' . $this->json->document->width . 'px;');
        $this->css->push('                height:' . $this->json->document->height . 'px;');
        $this->css->push('            }');
    }

    public function buildBodyHTML(): void
    {
        $this->body->push('           <div id="app">');

        foreach($this->json->document->components as $index=>$component){
            $encryptItem = sha1($index."XD");
            $ItemID = "ID".substr($encryptItem, -8, -1);

            $this->generateHTML($this->body,$component, $ItemID);
            $this->generateCSS($this->css, $component, $ItemID, false, $this->json->document->itemSpacing??0);

        }

        $this->body->push('            </div>');
    }

    public function buildFullHTML(string $dir, array $fonts,string $key): void
    {
        $this->content->push(Tags::DOC());
        $this->content->push(Tags::HTML(false, $dir));
        $this->content->push(Tags::HEAD());
        $this->content->push(Tags::META());
        $this->content->push(Tags::FONTS($fonts));
        $this->content->push(Tags::TITLE($key));
        $this->content->push(Tags::STYLE());
        $this->content->push($this->css->implode("\n"));
        $this->content->push(Tags::STYLE(true));
        $this->content->push(Tags::HEAD(true));
        $this->content->push(Tags::BODY());
        $this->content->push($this->body->implode("\n"));
        $this->content->push(Tags::BODY(true));
        $this->content->push(Tags::HTML(true));
    }
    public function generateHTML(&$body, $item, $id, $sub=false, $sub_spacing=[]): void
    {
        $spacing = [
            "collect" => 0,
            "left" => $item->left,
            "top" => $item->top,
            "width" => $item->width,
            "height" => $item->height,
        ];

        if($item->name === 'img'){
            if($sub){
                $spacing['collect'] = $spacing['top']-$sub_spacing['top'];
                $this->collectSpace($spacing);
            }
            else {
                $this->collectSpace($spacing);
            }
            $body->push(Tags::IMG($id, $item->src));
        }
        else if($item->type === 'TEXT'){
            if($sub){
                $spacing['collect'] = $spacing['top']-$sub_spacing['top'];
                $this->collectSpace($spacing);
            }
            else {
                $this->collectSpace($spacing);
            }

            $body->push(Tags::TEXT($item->name, $id, $item->text));
        }
        else if($item->name === 'a'){
            if($sub){
                $spacing['collect'] = $spacing['top']-$sub_spacing['top'];
            }
            $this->collectSpace($spacing);

            $body->push(Tags::A($id));
            if($item->type === 'TEXT'){
                $body->push(Tags::SPAN($item->text));
            }
            else {
                if(isset($item->placeholder)){
                    $body->push(Tags::SPAN($item->placeholder->text));
                }
                else {
                    foreach($item->components as $component){
                        $this->generateHTML($body, $component, $this->id($component->id), true, $spacing);
                    }
                }
            }
            $body->push(Tags::A($id, true));
        }
        else if(explode('-',$item->name)[0] === 'input'){
            if($sub){
                $spacing['collect'] = $spacing['top']-$sub_spacing['top'];
            }
            $this->collectSpace($spacing);

            if(isset($item->placeholder) ){
                $body->push(Tags::INPUT($id, $item->placeholder->text));
            }
            else {
                $body->push(Tags::INPUT($id));
            }
        }
        else if($item->name === 'button'){
            if($sub){
                $spacing['collect'] = $spacing['top']-$sub_spacing['top'];
            }
            $this->collectSpace($spacing);
            $body->push(Tags::BUTTON($id));
            if(isset($item->placeholder)){
                $body->push(Tags::SPAN($item->placeholder->text));
            }
            else {
                $this->collectSpace($spacing);
                foreach($item->components as $component){
                    $this->generateHTML($body, $component, $this->id($component->id), true, $spacing);
                }
            }
            $body->push(Tags::BUTTON($id, true));
        }
        else if($item->name === 'div') {
            if($sub){
                $spacing['collect'] = $spacing['top']-$sub_spacing['top'];
                $this->collectSpace($spacing);
            }
            else {
                $this->collectSpace($spacing);
            }
            $body->push(Tags::DIV(false, $id));
            foreach($item->components as $component){
                $this->generateHTML($body,$component, $this->id($component->id), true, $spacing);
            }
            $body->push(Tags::DIV(true));
        }
    }

    public function generateCSS(&$css,$item, $id, $sub=false, $margin=0): void
    {
        $this->css->push('            #'.$id.'{');
        if(isset($item->style)){
            if(isset($item->style->stocks) && count($item->style->stocks) > 0){
                $this->css->push('                border: 0.'.$item->style->stocks[0]->size.'rem solid '.$item->style->stocks[0]->rgba.';');
            }
            if(isset($item->style->corners) && count($item->style->corners) > 0){
                if(!(isset($item->style->stocks) && count($item->style->stocks) > 0)){
                    $this->css->push('                border: 1px solid transparent;');
                }
                $this->css->push('                border-radius:'.$item->style->corners[0]->topLeft.'px '.$item->style->corners[0]->topRight.'px '.$item->style->corners[0]->bottomRight.'px '.$item->style->corners[0]->bottomLeft.'px;');
            }
            if(isset($item->style->effects) && count($item->style->effects) > 0){
                if($item->style->effects[0]->type === 'DROP_SHADOW'){
                    $this->css->push('                box-shadow: '.$item->style->effects[0]->x.'px '.$item->style->effects[0]->y.'px '.$item->style->effects[0]->blur.'px 0px '.$item->style->effects[0]->rgba.';');
                }
            }
            if(isset($item->style->backgrounds) && count($item->style->backgrounds) > 0 && ($item->type !== 'TEXT')){
                $this->css->push('                background-color:'.$item->style->backgrounds[0]->rgba.';');
            }
            else {
                $this->css->push('                background-color:transparent;');
            }
        }
        else {
            if(isset($item->stocks) && count($item->stocks) > 0){
                $this->css->push('                border: 0.'.$item->stocks[0]->size.'rem solid '.$item->stocks[0]->rgba.';');
            }
            if(isset($item->corners) && count($item->corners) > 0){
                if(!(isset($item->stocks) && count($item->stocks) > 0)){
                    $this->css->push('                border: 1px solid transparent;');
                }
                $this->css->push('                border-radius:'.$item->corners[0]->topLeft.'px '.$item->corners[0]->topRight.'px '.$item->corners[0]->bottomRight.'px '.$item->corners[0]->bottomLeft.'px;');
            }
            if(isset($item->effects) && count($item->effects) > 0){
                if($item->effects[0]->type === 'DROP_SHADOW'){
                    $this->css->push('                box-shadow: '.$item->effects[0]->x.'px '.$item->effects[0]->y.'px '.$item->effects[0]->blur.'px 0px '.$item->effects[0]->rgba.';');
                }
            }
            if(isset($item->backgrounds) && count($item->backgrounds) > 0 && ($item->type !== 'TEXT')){
                $this->css->push('                background-color:'.$item->backgrounds[0]->rgba.';');
            }
            else {
                $this->css->push('                background-color:transparent;');
            }
        }

        $widthMin = 0;
        if($item->type === 'TEXT'){
            $this->css->push('                color:'.$item->backgrounds[0]->rgba.';');
            $this->css->push('                font-family:'.$item->{'font-family'}.';');
            $this->css->push('                font-size:'.$item->{'font-size'}.'px;');
            $this->css->push('                font-weight:'.$item->{'font-weight'}.';');
            $this->css->push('                line-height:'.$item->{'line-height'}.'px;');
            $this->css->push('                letter-spacing:'.$item->{'letter-spacing'}.'px;');
            if($item->Hcenter){
                $this->css->push('                text-align:center;');
            }
        }
        if(explode('-',$item->name)[0] === 'input'){
            $widthMin+=10;
            $this->css->push('                padding-left:5px;');
            $this->css->push('                padding-right:5px;');
        }
        if($item->name === 'button'){
            if($item->Hcenter){
                $this->css->push('                text-align:center;');
            }
        }
        if(($item->name === 'button' || explode('-',$item->name)[0] === 'input') && isset($item->placeholder)){
            if(count($item->placeholder->backgrounds)){
                $this->css->push('                color:'.$item->placeholder->backgrounds[0]->rgba.';');
            }
            $this->css->push('                font-family:'.$item->placeholder->{'font-family'}.';');
            $this->css->push('                font-size:'.$item->placeholder->{'font-size'}.'px;');
            $this->css->push('                font-weight:'.$item->placeholder->{'font-weight'}.';');
            $this->css->push('                line-height:'.$item->placeholder->{'line-height'}.'px;');
            $this->css->push('                letter-spacing:'.$item->placeholder->{'letter-spacing'}.'px;');
        }
        if($item->name === 'a' || $item->name === 'button' ){
            $this->css->push('                cursor: pointer;');
        }

        $heightMax = 0;
        if($item->name !== "button"){
            if(isset($item->paddingLeft)){
                $widthMin+= $item->paddingLeft;
                $this->css->push('                padding-left:'.$item->paddingLeft.'px;');
            }
            if(isset($item->paddingRight)){
                $widthMin+= $item->paddingRight;
                $this->css->push('                padding-right:'.$item->paddingRight.'px;');
            }
            if(isset($item->paddingTop)){
                $heightMax+= $item->paddingTop;
                $this->css->push('                padding-top:'.$item->paddingTop.'px;');
            }
            if(isset($item->paddingBottom)){
                $heightMax+= $item->paddingBottom;
                $this->css->push('                padding-bottom:'.$item->paddingBottom.'px;');
            }
            if(isset($item->itemSpacing)){
                $this->css->push('                margin-bottom:'.$item->itemSpacing.'px;');
            }

        }
        if($margin){
            $this->css->push('                margin-top:'.$margin.'px;');
        }
        if($item->Hcenter){
            $this->css->push('                margin-right:auto;');
            $this->css->push('                margin-left:auto;');
        }
        if(isset($item->flex)){
            if($item->align === 'HORIZONTAL'){
                if($item->name === 'button'){
                    $this->css->push('                text-align:center;');
                }
                else {
                    $this->css->push('                display:flex;');
                    $this->css->push('                flex-direction:row;');
                }

            }
            if($item->flex === 'SPACE_BETWEEN'){
                $this->css->push('                justify-content:space-between;');
            }
        }

        $this->css->push('                width:'.$item->width-$widthMin.'px;');
        $this->css->push('                height:'.$item->height.'px;');
        $this->css->push('            }');


        if(count($item->components) > 0){
            foreach($item->components as $component){
                $encrypt = sha1($component->id);
                $bodyID = "ID".substr($encrypt, -8, -1);
                $this->generateCSS($css, $component, $bodyID, true, $item->itemSpacing ?? 0);
            }
        }

    }

    public function save(string $path,string $key): void
    {
        // File Set
        File::delete($path.'/'.$key.'.html');
        File::put($path.'/'.$key.'.html', $this->content->implode("\n"));
    }
}
