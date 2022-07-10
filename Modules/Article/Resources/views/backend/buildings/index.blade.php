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
        @media screen and (max-width: 840px) {
            .ss {
                width: 100%;
            }

            .dataTables_length {
                text-align: left;
            }
        }
        @media screen and (max-width: 1030px) {
            .card-title {
                display: none;
            }
            .text-medium-emphasis {
                display: none;
            }
        }
        .img-size{
            /* 	padding: 0;
                margin: 0; */
            height: 450px;
            width: 100%;
            background-size: cover;
            overflow: hidden;
        }
        .modal-content#gallary {
            width: 100%;
            border:none;
        }
        .modal-body#gallary1 {
            padding: 0;
        }

        .carousel-control-prev-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23009be1' viewBox='0 0 8 8'%3E%3Cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3E%3C/svg%3E");
            width: 30px;
            height: 48px;
        }
        .carousel-control-next-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23009be1' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E");
            width: 30px;
            height: 48px;
        }
    </style>
    <div class="card">
        <div class="card-body" style="overflow: scroll">

            <x-backend.section-header>
                <i class="{{ $module_icon }}"></i> {{ __($module_title) }} <small class="text-muted">{{ __($module_action) }}</small>

                <x-slot name="subtitle">
                    @lang("Address Management Dashboard")
                </x-slot>
                <x-slot name="toolbar">
                    @can('add_'.$module_name)

                    @endcan
                        <button type="button" style="margin: 10px" class="btn btn-primary ss" data-toggle="modal" data-target="#myModal">
                            Setting Prefix
                        </button>
                        <button type="button" style="margin: 10px" class="btn btn-warning ss" data-toggle="modal" data-target="#myModal1">
                            Setting Suffix
                        </button>

                        <button type="button" style="margin: 10px" class="btn btn-info ss" onclick="scraping()">
                            Auto Scheduling
                        </button>
                    @can('restore_'.$module_name)

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
                            <th width="20%">
                                Address
                            </th>
                            <th width="10%">
                                statusType
                            </th>
                            <th width="10%">
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
                            <th class="text-end" width="10%">
                                Action
                            </th>
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
    <!-- gallary-->
    <div class="modal fade" id="largeModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="gallary">
                <div class="modal-body" id="gallary1">
                    <!-- carousel -->
                    <div id='carouselExampleIndicators' class='carousel slide' data-ride='carousel'>
                        <ol class='carousel-indicators' id="build_next">

                        </ol>
                        <div class='carousel-inner' id="build_slider_images">

                        </div>
                        <a class='carousel-control-prev' href='#carouselExampleIndicators' role='button' data-slide='prev'>
                      <span class='carousel-control-prev-icon' aria-hidden='true'></span>
                    <span class='sr-only'>Previous</span>
                </a>
                <a
                    class='carousel-control-next'
                    href='#carouselExampleIndicators'
                    role='button'
                    data-slide='next'
                >
              <span
                  class='carousel-control-next-icon'
                  aria-hidden='true'
              ></span>
                            <span class='sr-only'>Next</span>
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
        var param = location.search;
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: true,
            responsive: true,
            ajax: '{{ url("admin/$module_name/building_get") }}'+param,
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
                },
                {
                    data: 'action',
                    name: 'action'
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

        function getImage(ID) {
            $.ajax('{{ route("backend.$module_name.getImage") }}', {
                type: 'get',
                data: {ID: ID},
                success: function (data, status, xhr) {
                    var html_build_next = '';
                    var html_build_image = '';
                    $('#build_next').empty();
                    $('#html_build_image').empty();
                    data.map((item, index) => {
                        if(index == 0) {
                            html_build_next = html_build_next + '<li data-target="#carouselExampleIndicators" data-slide-to="'+ index +'" class="active"></li>';
                            html_build_image = html_build_image + '<div class="carousel-item active"><img class="img-size" src="'+ item.image_path +'"></div>';
                        }else {
                            html_build_next = html_build_next + '<li data-target="#carouselExampleIndicators" data-slide-to="'+ index +'"></li>';
                            html_build_image = html_build_image + '<div class="carousel-item"><img class="img-size" src="'+ item.image_path +'"></div>';
                        }
                    })
                    $('#build_next').append(html_build_next);
                    $('#build_slider_images').append(html_build_image);
                },
                error: function (jqXhr, textStatus, errorMessage) {

                }
            });
        }
        function scraping() {
            $.ajax('{{ route("backend.$module_name.scraping") }}', {
                type: 'get',
                data: {status: 1},
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

