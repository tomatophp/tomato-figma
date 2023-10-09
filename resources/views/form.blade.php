<div class="flex flex-col space-y-4">
    <x-splade-input name="url" :label="__('Figma URL')" :placeholder="__('Input Your Figma Project URL With Selected Node')" required/>
    <x-splade-checkbox name="dir" :label="__('Support RTL?')"/>
    <x-splade-group name="type" :label="__('Preferred Generated Template')" inline>
        <x-splade-radio name="type" value="html" :label="__('HTML')" />
        <x-splade-radio name="type" value="tailwind" :label="__('Tailwind')" />
    </x-splade-group>
    <x-tomato-admin-repeater :options="['font']" name="fonts" :label="__('Allow Google Fonts URL')">
        <x-splade-input v-model="repeater.main[key].font" :label="__('Font Name')" :placeholder="__('Font Name From Google')"></x-splade-input>
    </x-tomato-admin-repeater>

    <x-tomato-admin-submit :label="__('Open File')" spinner/>
</div>
