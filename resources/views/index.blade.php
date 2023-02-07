<x-tomato-admin-layout>
    <x-slot name="header">
        Figma Converter
    </x-slot>

    <div class="my-4 overflow-scroll bg-white border border-gray-300 shadow-sm rounded-xl filament-tables-container dark:bg-gray-800 dark:border-gray-700">
        <div class="py-4 px-4 ">
            <x-splade-form :default="[
                'type' => 'html',
                'fonts' => [],
                'dir' => false
            ]" action="{{route('admin.figma.files')}}" method="POST">
                <div class="flex flex-col space-y-4">
                    <x-splade-input name="url" label="Figma URL" placeholder="Input Your Figma Project URL With Selected Node" required/>
                    <x-splade-checkbox name="dir" label="Support RTL?"/>
                    <x-splade-group name="type" label="Preferred Generated Template" inline>
                        <x-splade-radio name="type" value="html" label="HTML" />
                        <x-splade-radio name="type" value="tailwind" label="Tailwind" />
                    </x-splade-group>
                    <x-tomato-repeater :options="['font']" name="fonts" label="Allow Google Fonts URL">
                        <x-splade-input v-model="repeater.main[key].font" label="Font Name" placeholder="Font Name From Google"></x-splade-input>
                    </x-tomato-repeater>
                    <button type="submit" class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">Open File</button>
                </div>
            </x-splade-form>
        </div>
        @if(isset($body))
            <div class="grid grid-cols-12 gap-2 px-4 px-4">
                <div class="col-span-12">
                    <h1>ScreenShot</h1>
                    <a href="{{$url}}" target="_blank">
                        <img class="mx-auto py-4 px-4" src="{{$body->screenshot}}" alt="">
                    </a>
                </div>
            </div>
        @endif

    </div>
</x-tomato-admin-layout>
