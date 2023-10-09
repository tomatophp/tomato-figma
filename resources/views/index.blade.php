<x-tomato-admin-layout>
    <x-slot:header>
        {{__('Figma Converter')}}
    </x-slot:header>

    <div class="my-4 overflow-scroll bg-white border border-gray-300 shadow-sm rounded-xl filament-tables-container dark:bg-gray-800 dark:border-gray-700">
        <div class="py-4 px-4 ">
            <x-splade-form :default="[
                'type' => 'html',
                'fonts' => [],
                'dir' => false
            ]" action="{{route('admin.figma.files')}}" method="POST">
                @include('tomato-figma::form')
            </x-splade-form>
        </div>
        @if(isset($body))
            <div class="grid grid-cols-12 gap-2 px-4 px-4">
                <div class="col-span-12">
                    <h1>{{__('ScreenShot')}}</h1>
                    <a href="{{$url}}" target="_blank">
                        <img class="mx-auto py-4 px-4" src="{{$body->screenshot}}" alt="">
                    </a>
                </div>
            </div>
        @endif

    </div>
</x-tomato-admin-layout>
