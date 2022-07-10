<div class="text-end">
    @can('edit_'.$module_name)
        <x-buttons.edit route='{!!route("backend.$module_name.edit", $data)!!}' title="{{__('Edit')}}" small="true" />
        <x-buttons.show route='{!!route("backend.$module_name.show", $data)!!}' title="{{__('Show')}} City" small="true" />
@endcan
<!-- <x-buttons.show route='{!!route("backend.$module_name.show", $data)!!}' title="{{__('Show')}} City" small="true" /> -->
</div>
