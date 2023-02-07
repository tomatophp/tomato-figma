<!doctype html>
<html lang="en" dir="{{$dir}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$name}}</title>
    @foreach($fonts as $font)
    <link href='https://fonts.googleapis.com/css?family={{$font}}&display=swap' rel='stylesheet'>
    @endforeach
    <style>
        body{margin: 0; padding: 0;}
        #app {
            background-color: {{$bg}};
            position: relative;
            overflow: hidden;
            width: {{$width}}px;
            height: {{$height}}px;
            @if(isset($paddingLeft))
            padding-left: {{$paddingLeft}}px;
            @endif
             @if(isset($paddingRight))
            padding-right: {{$paddingRight}}px;
            @endif
            @if(isset($paddingTop))
            padding-top: {{$paddingTop}}px;
            @endif
            @if(isset($paddingBottom))
            padding-bottom: {{$paddingBottom}}px;
            @endif
        }
        {{$style}}
    </style>
</head>
<body>
    <div id="app">
        {{$body}}
    </div>
</body>
</html>
