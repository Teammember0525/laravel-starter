@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ __($module_title) }} @endsection

@section('breadcrumbs')
    <x-backend-breadcrumbs>
        <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>{{ __($module_title) }}</x-backend-breadcrumb-item>
    </x-backend-breadcrumbs>
@endsection

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/notification.css') }}">
    <style>
        .notification {
            z-index: 1000000!important;
        }
    </style>
    <div class="card">
        <div class="card-body">

            <x-backend.section-header>
                <i class="{{ $module_icon }}"></i> {{ __($module_title) }} <small class="text-muted">{{ __($module_action) }}</small>

                <x-slot name="subtitle">
                    @lang("Address Management Dashboard")
                </x-slot>
                <x-slot name="toolbar">
                    @can('add_'.$module_name)

                    @endcan
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                            Setting Prefix
                        </button>
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModal1">
                            Setting Suffix
                        </button>

                        <button type="button" class="btn btn-info">
                            Auto Scheduling
                        </button>
                    @can('restore_'.$module_name)
                        <div class="btn-group">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-coreui-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu">
{{--                                <li>--}}
{{--                                    <a class="dropdown-item" href='{{ route("backend.$module_name.trashed") }}'>--}}
{{--                                        <i class="fas fa-eye-slash"></i> View trash--}}
{{--                                    </a>--}}
{{--                                </li>--}}
                                <!-- <li>
                                    <hr class="dropdown-divider">
                                </li> -->
                            </ul>
                        </div>
                    @endcan
                </x-slot>
            </x-backend.section-header>

            <div class="row mt-4">
                <div class="col">
                    <table id="datatable" class="table table-bordered table-hover table-responsive-sm">
                        <thead>
                        <tr>
                            <th>
                                ID
                            </th>
                            <th>
                                ZpID
                            </th>
                            <th width="10%">
                                Image
                            </th>
                            <th>
                                Address
                            </th>
                            <th width="10%">
                                statusType
                            </th>
                            <th width="20%">
                                statusText
                            </th>
                            <th>
                                Beds
                            </th>
                            <th>
                                Baths
                            </th>
                            <th>
                                Area
                            </th>
                            <th>
                                Price
                            </th>
{{--                            <th class="text-end">--}}
{{--                                Action--}}
{{--                            </th>--}}
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-7">
                    <div class="float-left">

                    </div>
                </div>
                <div class="col-5">
                    <div class="float-end">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Setting Prefix</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <input type="text" class="form-control" id="content" placeholder="{{$add_state == '' ? 'Enter Your Prefix' : $add_state}}">
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    @if($add_state == '')
                        <button type="button" class="btn btn-success" onclick="sending('true', 1)" id="success" data-dismiss="modal">Save</button>
                    @else
                        <button type="button" class="btn btn-success" onclick="sending('false', 1)" id="success" data-dismiss="modal">edit</button>
                    @endif
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

    <!--suffix -->
    <div class="modal" id="myModal1">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Setting Suffix</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <input type="text" class="form-control" id="content1" placeholder="{{$add_suffix == '' ? 'Enter Your Suffix' : $add_suffix}}">
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    @if($add_suffix == '')
                        <button type="button" class="btn btn-success" onclick="sending('true', 2)" id="success" data-dismiss="modal">Save</button>
                    @else
                        <button type="button" class="btn btn-success" onclick="sending('false', 2)" id="success" data-dismiss="modal">edit</button>
                    @endif
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
@endsection

@push ('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">

@endpush

@push ('after-scripts')
    <!-- DataTables Core and Extensions -->

    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <script type="text/javascript">
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: true,
            responsive: true,
            ajax: '{{ route("backend.$module_name.building_get") }}',
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'home_id',
                    name: 'home_id'
                },
                {
                    data: 'imageSrc',
                    name: 'imageSrc'
                },
                {
                    data: 'address',
                    name: 'address'
                },
                {
                    data: 'statusType',
                    name: 'statusType'
                },
                {
                    data: 'statusText',
                    name: 'statusText'
                },
                {
                    data: 'beds',
                    name: 'beds'
                },
                {
                    data: 'baths',
                    name: 'baths'
                },
                {
                    data: 'area',
                    name: 'area'
                },
                {
                    data: 'price',
                    name: 'price'
                }
            ]
        });

    </script>


    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="{{ asset('js/notification.js') }}"></script>
    <script>

        function sending(status, type) {
            var content = '';
            if(type == 1) {
                content = $('#content').val();
            }else if (type == 2) {
                content = $('#content1').val();
            }
            $.ajax('{{ route("backend.$module_name.settings") }}', {
                type: 'get',
                data: {status: status, content: content, type:type},
                success: function (data, status, xhr) {
                    if(data == 'save_success'){
                        newNotification('Save Prefix Words', 1);
                        window.location.reload();
                    }else {
                        newNotification('Change Prefix Words', 1);
                        window.location.reload();
                    }
                },
                error: function (jqXhr, textStatus, errorMessage) {

                }
            });
        }
    </script>
@endpush

