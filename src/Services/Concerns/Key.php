<?php

namespace TomatoPHP\TomatoFigma\Services\Concerns;

class Key
{
    /**
     * @var string
     */
    public string $key;
    /**
     * @var string
     */
    public string $title;
    /**
     * @var string
     */
    public string $node;

    /**
     * @param string $url
     * @return static
     */
    public static function make(string $url): static
    {
        return (new static)->generate($url);
    }

    /**
     * @param string $url
     * @return $this
     */
    public function generate(string $url): static
    {
        //Clear Figma Domain from the URL
        $clearURL = str_replace('https://www.figma.com/file/','', $url);

        //Explode The String By /
        $explodeNodeId = explode('/', $clearURL);

        //Fetch File Key
        $this->key = $explodeNodeId[0];

        //Explode Title And Ids
        $explodeTitleAndIds = explode('?node-id=', $explodeNodeId[1]);

        //Fetch Title
        $this->title = $explodeTitleAndIds[0];

        //Fetch Key
        $this->node = $explodeTitleAndIds[1];

        return $this;
    }
}
