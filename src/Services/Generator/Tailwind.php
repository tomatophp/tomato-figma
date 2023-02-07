<?php

namespace TomatoPHP\TomatoFigma\Services\Generator;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use TomatoPHP\TomatoFigma\Services\Helpers\CheckFolder;
use TomatoPHP\TomatoFigma\Services\Helpers\IDGenerator;
use TomatoPHP\TomatoFigma\Services\Helpers\Spacing;
use TomatoPHP\TomatoFigma\Services\Helpers\Tags;
use stdClass;

class Tailwind implements Generator
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

    public function buildBodyHTML(): void
    {
        $this->body->push('           <div id="app">');

        foreach($this->json->document->components as $index=>$component){
            $this->generateHTML($this->body,$component, $this->id($index."XD"), false, [],$this->json->document->itemSpacing??0);
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
        $this->content->push("      <script src=\"https://cdn.tailwindcss.com\"></script>\n");
        $this->content->push(Tags::STYLE());
        $this->content->push($this->css->implode("\n"));
        $this->content->push(Tags::STYLE(true));
        $this->content->push(Tags::HEAD(true));
        $this->content->push(Tags::BODY());
        $this->content->push($this->body->implode("\n"));
        $this->content->push(Tags::BODY(true));
        $this->content->push(Tags::HTML(true));
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

    public function generateTailwindStyle($body, $item, $id, $sub=false, $sub_spacing=[], $margin=0): string
    {
        $style = "";
        if(isset($item->style)){
            if(isset($item->style->stocks) && count($item->style->stocks) > 0){
                $style .= "\n                   border-".$item->style->stocks[0]->size." ";
                $style .= "\n                   border-[" .$item->style->stocks[0]->hex ."] ";
            }
            if(isset($item->style->corners) && count($item->style->corners) > 0){
                $style .= "\n                   rounded-tl-[".$item->style->corners[0]->topLeft. "px] ";
                $style .= "\n                   rounded-tr-[".$item->style->corners[0]->topRight. "px] ";
                $style .= "\n                   rounded-br-[".$item->style->corners[0]->bottomRight. "px] ";
                $style .= "\n                   rounded-bl-[".$item->style->corners[0]->bottomLeft. "px] ";
            }
            if(isset($item->style->effects) && count($item->style->effects) > 0){
                if($item->style->effects[0]->type === 'DROP_SHADOW'){
                    $style .= "\n                   drop-shadow-[".$item->style->effects[0]->x."px_".$item->style->effects[0]->y."px_".$item->style->effects[0]->blur."px_". $item->style->effects[0]->rgba ."] ";
                }
            }
            if(isset($item->style->backgrounds) && count($item->style->backgrounds) > 0 && ($item->type !== 'TEXT')){
                $style .= "\n                   bg-[".$item->style->backgrounds[0]->hex."] ";
            }
            else {
                $style .= "\n                   bg-transparent ";
            }
        }
        else {
            if(isset($item->stocks) && count($item->stocks) > 0){
                $style .= "\n                   border ";
                $style .=" \n                   border-[0.".$item->stocks[0]->size."rem] ";
                $style .=" \n                   border-[".$item->stocks[0]->hex."] ";
            }
            if(isset($item->corners) && count($item->corners) > 0){
                $style .= "\n                   rounded-tl-[".$item->corners[0]->topLeft. "px] ";
                $style .= "\n                   rounded-tr-[".$item->corners[0]->topRight. "px] ";
                $style .= "\n                   rounded-br-[".$item->corners[0]->bottomRight. "px] ";
                $style .= "\n                   rounded-bl-[".$item->corners[0]->bottomLeft. "px] ";
            }
            if(isset($item->effects) && count($item->effects) > 0){
                if($item->effects[0]->type === 'DROP_SHADOW'){
                    $style .= "\n                   drop-shadow-[".$item->effects[0]->x."px_".$item->effects[0]->y."px_".$item->effects[0]->blur."px_". $item->effects[0]->rgba ."] ";
                }
            }
            if(isset($item->backgrounds) && count($item->backgrounds) > 0 && ($item->type !== 'TEXT')){
                $style .= "\n                   bg-[".$item->backgrounds[0]->hex."] ";
            }
            else {
                $style .= "\n                   bg-transparent ";
            }
        }

        $widthMin = 0;
        if($item->type === 'TEXT'){
            $style .= "\n                   text-[".$item->backgrounds[0]->hex."] ";
            $style .= "\n                   font-[".$item->{'font-family'}."] ";
            $style .= "\n                   text-[".$item->{'font-size'}."px] ";
            $style .= "\n                   font-[".$item->{'font-weight'}."] ";
            $style .= "\n                   leading-[".$item->{'line-height'}."px] ";
            $style .= "\n                   tracking-[".$item->{'letter-spacing'}."px] ";
            if($item->Hcenter){
                $style .= "                     text-center ";
            }
        }
        if(explode('-',$item->name)[0] === 'input'){
            $widthMin+=20;
            $style .= "\n                   px-[10px]";
        }
        if($item->name === 'button'){
            if($item->Hcenter){
                $style .= "\n                   text-center ";
            }
        }
        if(($item->name === 'button' || explode('-',$item->name)[0] === 'input') && isset($item->placeholder)){
            if(count($item->placeholder->backgrounds)){
                $style .= "\n                   text-[".$item->placeholder->backgrounds[0]->hex."] ";
            }
            $style .= "\n                   font-[".$item->placeholder->{'font-family'}."] ";
            $style .= "\n                   text-[".$item->placeholder->{'font-size'}."px] ";
            $style .= "\n                   font-[".$item->placeholder->{'font-weight'}."] ";
            $style .= "\n                   leading-[".$item->placeholder->{'line-height'}."px] ";
            $style .= "\n                   tracking-[".$item->placeholder->{'letter-spacing'}."px] ";
        }
        if($item->name === 'a' || $item->name === 'button' ){
            $style .= "\n                   cursor-pointer ";
        }

        $heightMax = 0;
        if($item->name !== "button"){
            if(isset($item->paddingLeft)){
                $widthMin+= $item->paddingLeft;
                $style .= "\n                   pl-[".$item->paddingLeft."px] ";
            }
            if(isset($item->paddingRight)){
                $widthMin+= $item->paddingRight;
                $style .= "\n                   pr-[".$item->paddingRight."px] ";
            }
            if(isset($item->paddingTop)){
                $heightMax+= $item->paddingTop;
                $style .= "\n                   pt-[".$item->paddingTop."px] ";
            }
            if(isset($item->paddingBottom)){
                $heightMax+= $item->paddingBottom;
                $style .= "\n                   pb-[".$item->paddingBottom."px] ";
            }
        }
        if(isset($item->itemSpacing)){
            $style .= "\n                   mt-[".$item->itemSpacing."px] ";
        }

        if($margin){
            $style .= "\n                   mb-[".$margin."px] ";
        }
        if($item->Hcenter){
            $style .= "\n                   mx-auto ";
        }
        if(isset($item->flex)){
            if($item->align === 'HORIZONTAL'){
                if($item->name === 'button'){
                    $style .= "\n                   text-center ";
                }
                else {
                    $style .= "\n                   flex ";
                }

            }
            if($item->flex === 'SPACE_BETWEEN'){
                $style .= "\n                 flex ";
                $style .= "\n                 justify-between ";
            }
        }

        $style .= "\n                   w-[".($item->width)."px] ";
        $style .= "\n                   h-[".($item->height)."px] ";

        return $style;
    }
    public function generateHTML(&$body, $item, $id, $sub=false, $sub_spacing=[], $margin=0): void
    {
        $style = $this->generateTailwindStyle($body, $item, $id, $sub, $sub_spacing, $margin);
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
            }
            $this->collectSpace($spacing);
            $body->push(Tags::IMG($id, $item->src, $style));
        }
        else if($item->type === 'TEXT'){
            if($sub){
                $spacing['collect'] = $spacing['top']-$sub_spacing['top'];
            }
            $this->collectSpace($spacing);

            $body->push(Tags::TEXT($item->name, $id, $item->text, $style));
        }
        else if($item->name === 'a'){
            if($sub){
                $spacing['collect'] = $spacing['top']-$sub_spacing['top'];
            }
            $this->collectSpace($spacing);

            $body->push(Tags::A($id, false, $style));
            if($item->type === 'TEXT'){
                $body->push(Tags::SPAN($item->text));
            }
            else {
                if(isset($item->placeholder)){
                    $body->push(Tags::SPAN($item->placeholder->text));
                }
                else {
                    foreach($item->components as $component){
                        $this->generateHTML($body, $component, $this->id($component->id), true, $spacing, $item->itemSpacing??0);
                    }
                }
            }
            $body->push(Tags::A($id, true, $style));
        }
        else if(explode('-',$item->name)[0] === 'input'){
            if($sub){
                $spacing['collect'] = $spacing['top']-$sub_spacing['top'];
            }
            $this->collectSpace($spacing);

            if(isset($item->placeholder) ){
                $body->push(Tags::INPUT($id, $item->placeholder->text, 'text', $style));
            }
            else {
                $body->push(Tags::INPUT($id, null, 'text', $style));
            }
        }
        else if($item->name === 'button'){
            if($sub){
                $spacing['collect'] = $spacing['top']-$sub_spacing['top'];
            }
            $this->collectSpace($spacing);
            $body->push(Tags::BUTTON($id, false, null, $style));
            if(isset($item->placeholder)){
                $body->push(Tags::SPAN($item->placeholder->text));
            }
            else {
                $this->collectSpace($spacing);
                foreach($item->components as $component){
                    $this->generateHTML($body, $component, $this->id($component->id), true, $spacing, $item->itemSpacing??0);
                }
            }
            $body->push(Tags::BUTTON($id, true));
        }
        else if($item->name === 'div') {
            if($sub){
                $spacing['collect'] = $spacing['top']-$sub_spacing['top'];
            }
            $this->collectSpace($spacing);

            $body->push(Tags::DIV(false, $id, $style));
            foreach($item->components as $component){
                $this->generateHTML($body,$component, $this->id($component->id), true, $spacing, $item->itemSpacing??0);
            }
            $body->push(Tags::DIV(true));
        }
    }

    public function save(string $path,string $key): void
    {
        // File Set
        File::delete($path.'/'.$key.'.html');
        File::put($path.'/'.$key.'.html', $this->content->implode("\n"));
    }

}
