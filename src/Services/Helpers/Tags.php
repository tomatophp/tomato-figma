<?php

namespace TomatoPHP\TomatoFigma\Services\Helpers;

class Tags
{
    public static function DOC(): string
    {
        return "<!DOCTYPE html>";
    }
    public static function HTML(bool $end=false, string $dir="rtl"): string
    {
        return $end ? "</html>" : "<html lang=\"en\" dir='".$dir."'>";
    }
    public static function HEAD(bool $end = false): string
    {
        return $end ? "    </head>" : "    <head>";
    }
    public static function META(): string
    {
        $meta = "        <meta charset=\"UTF-8\">\n";
        $meta .= "        <meta name=\"viewport\" content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">\n";
        return $meta;
    }
    public static function FONTS($fonts): string
    {
        $getFonts = "";
        foreach($fonts as $font){
            $getFonts .="        <link href='https://fonts.googleapis.com/css?family=".$font."&display=swap' rel='stylesheet'>\n";
        }

        return $getFonts;
    }
    public static function TITLE(string $title): string
    {
        return "        <title>".$title."</title>\n";
    }
    public static function STYLE(bool $end = false): string
    {
        return $end? "        </style>\n" : "        <style>";
    }
    public static function BODY(bool $end = false, string $class=null): string
    {
        return $end? "        </body>" : "        <body class='$class'>";
    }

    public static function IMG(string $id, string $src, string $class=null): string
    {
        $img ="               <div id=\"".$id."\">\n";
        $img .="                   <img src=\"".$src."\" alt=\"".$id."\"  class='$class'>\n";
        $img .="               </div>\n";
        return $img;
    }

    public static function TEXT(string $tag, string $id, string $text, string $class=null): string
    {
        $h ="               <".$tag." id=\"".$id."\" class='$class'>\n";
        $h .="                   <span>".$text."</span>\n";
        $h .="               </".$tag.">\n";
        return $h;
    }

    public static function SPAN(string $text, string $id=null, string $class=null): string
    {
        $span =$id?"               <span id=\"".$id."\" class='$class'>\n": "               <span>\n";
        $span .="                   ".$text."\n";
        $span .="               </span>\n";
        return $span;
    }

    public static function A(string $id, bool $end=false, string $class=null): string
    {
        return $end ? "               </a>\n" : "               <a id=\"".$id."\" href=\"#\" class='$class'>\n";
    }

    public static function INPUT(string $id, string $placeholder=null,string $type='text', string $class=null): string
    {
        return "               <input type=\"".$type."\" placeholder=\"".$placeholder."\" id=\"".$id."\" class='$class'>\n";
    }

    public static function BUTTON(string $id,bool $end=false, string $placeholder=null, string $class=null): string
    {
        return $end? "               </button>\n" : "               <button type=\"button\" id=\"".$id."\" class='$class'>\n";
    }

    public static function DIV(bool $end=false, string $id=null, string $class=null): string
    {
        return $end ? "               </div>\n" : "               <div id=\"".$id."\" class='$class'>\n";
    }
}
