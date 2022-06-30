<?php

namespace Modules\Article\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Article\Http\Requests\Backend\PostsRequest;
use Yajra\DataTables\DataTables;

class BuildingsController extends Controller
{
    use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Buildings';

        // module name
        $this->module_name = 'buildings';

        // directory path of the module
        $this->module_path = 'buildings';

        // module icon
        $this->module_icon = 'fas fa-sitemap';

        // module model name, path
        $this->module_model = "Modules\Article\Entities\Post";
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'List';

        $$module_name = $module_model::latest()->paginate();

        Log::info(label_case($module_title.' '.$module_action).' | User:'.Auth::user()->name.'(ID:'.Auth::user()->id.')');
        $first = DB::table('home_infos')->orderBy('id')->first();
        $add_state = '';
        if($first->perfix_word != null && $first->perfix_word != '') {
            $add_state = $first->perfix_word;
        }
        return view(
            "article::backend.$module_path.index",
            compact('module_title', 'module_name', "$module_name", 'module_icon', 'module_name_singular', 'module_action', 'add_state')
        );
    }
    public function building_get() {

        $data = DB::table('home_infos')->select('id', 'zpid', 'home_id', 'statusType', 'statusText', 'price', 'address', 'imageSrc','beds', 'baths', 'area')->get();
        return DataTables::of($data)
            ->editColumn('imageSrc', function ($info) {
                return '<img src="' .$info->imageSrc.'" max-width="100px"/>';
            })
            ->rawColumns(['imageSrc'])
            ->make(true);

    }

    public function settings(Request $request) {

    }
    public function show(Request $request) {

        $content = $request->get('content');

        if($request->get('status') == 'true') {
            DB::table('home_infos')->update(['perfix_word' => $content]);
            DB::update('update home_infos set address = CONCAT("'. $content .'", address)');
            return 'save_success';
        }elseif ($request->get('status') == 'false') {
            $first = DB::table('home_infos')->orderBy('id')->first();
            $search = $first->perfix_word;
            DB::update('update home_infos set address = REPLACE(address,"'. $search .'", "'. $content .'")');
            DB::table('home_infos')->update(['perfix_word' => $content]);
            return 'update_success';
        }
    }

}
