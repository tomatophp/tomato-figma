<?php

namespace TomatoPHP\TomatoFigma\Services\Concerns;

use TomatoPHP\TomatoFigma\Services\Figma;

class Response
{
    /**
     * @var object|null
     */
    public ?object $children;
    /**
     * @var string|null
     */
    public ?string $key;
    /**
     * @var string|null
     */
    public ?string $id;
    /**
     * @var string|null
     */
    public ?string $image;


    /**
     * @param string $key
     * @param string $node
     * @return static|\Exception|bool
     */
    public static function make(string $key, string $node): static|\Exception|bool
    {
        return (new static)->get($key, $node);
    }


    /**
     * @param string $key
     * @param string $node
     * @return $this|\Exception|bool
     */
    public function get(string $key, string $node): static|\Exception|bool
    {
        $figma = new Figma();
        $response = $figma->getElementById($key, $node);

        if(isset($response->status) && $response->status == 403){
            return false;
        }
        else {
            foreach($response->nodes as $keyItem=>$item) {
                if (count($item->document->children) <= 1) {
                    $this->children = $item->document->children[0];
                }
                else {
                    $this->children = $item->document;
                }
                $this->key = $keyItem;
                $this->id = $item->document->id;
                $this->image = $response->thumbnailUrl;
            }
        }


        return $this;
    }
}
