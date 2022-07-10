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
use function Spatie\Backup\BackupDestination\exists;
use Applitools\RectangleSize;
use Applitools\Selenium\Eyes;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
class BuildingsController extends Controller
{
    use Authorizable;
    protected $url = 'https://www.zillow.com/homedetails/353-Mananai-Pl-36R-Honolulu-HI-96818/2062652611_zpid/';
    protected $webDriver;
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
    public function index(Request $request)
    {

        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'List';
        if($request->get('address') != '' && $request->get('address') != null) {
            $module_title = $request->get('address');
        }
        $$module_name = $module_model::latest()->paginate();

        Log::info(label_case($module_title.' '.$module_action).' | User:'.Auth::user()->name.'(ID:'.Auth::user()->id.')');
        $first = DB::table('home_infos')->orderBy('id')->first();
        $add_state = '';
        $add_suffix = '';
        if($first->perfix_word != null && $first->perfix_word != '') {
            $add_state = $first->perfix_word;

        }
        if($first->subfix_word != null && $first->subfix_word != '') {
            $add_suffix = $first->subfix_word;
        }
        return view(
            "article::backend.$module_path.index",
            compact('module_title', 'module_name', "$module_name", 'module_icon', 'module_name_singular', 'module_action', 'add_state', 'add_suffix')
        );
    }
    public function building_get(Request $request) {
        $param = $request->get('address');

        if($param != '' && $param != null) {

            $get_city_id = DB::table('categories')->where('name', $param)->first();
            if(DB::table('home_infos')->where('city_id', $get_city_id->id)->exists()) {
                $data = DB::table('home_infos')->select('id', 'zpid', 'home_id', 'statusType', 'statusText', 'price', 'address', 'imageSrc','beds', 'baths', 'area', 'city_id')->where('city_id', $get_city_id->id)->get();

                return DataTables::of($data)
                    ->addColumn('action', function ($data) {
                        $module_name = $this->module_name;
                        if($data->city_id == 3)
                            return ('<button class="btn btn-primary" style="padding: 9px;margin-right: 10px" onclick="buildingEdit('.$data->id.')" data-toggle="modal" data-target="#myModal_edit_building"><i class="fa fa-edit"></i></button><button class="btn btn-success " onclick="getImage('. $data->id .')" data-toggle="modal" data-target="#largeModal" style="padding: 9px;padding-left: 11px;padding-right: 11px;"><i class="fa fa-play"></i></button>');
                        else
                            return ('<button class="btn btn-primary" style="padding: 9px;" onclick="buildingEdit('.$data->id.')" data-toggle="modal" data-target="#myModal_edit_building"><i class="fa fa-edit"></i></button>');
                    })
                    ->editColumn('imageSrc', function ($info) {
                        return '<img src="' .$info->imageSrc.'" width="100px"/>';
                    })
                    ->editColumn('statusText', function ($info) {
                        if($info->statusText == 'Active')
                            return '<p style="background-color: rgb(255, 90, 80);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusText .'</p>';
                        else if($info->statusText == 'Sold')
                            return '<p style="background-color: rgb(255, 210, 55);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusText .'</p>';
                        else
                            return '<p style="background-color: rgb(152, 93, 255);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusText .'</p>';
                    })
                    ->editColumn('statusType', function ($info) {
                        if($info->statusType == 'FOR_SALE')
                            return '<p style="background-color: rgb(255, 90, 80);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusType .'</p>';
                        else if($info->statusType == 'SOLD')
                            return '<p style="background-color: rgb(255, 210, 55);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusType .'</p>';
                        else
                            return '<p style="background-color: rgb(152, 93, 255);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusType .'</p>';
                    })
                    ->rawColumns(['imageSrc', 'action', 'statusType', 'statusText'])
                    ->make(true);
            }else {
                return DataTables::of([])->make(true);
            }

        }else {
            $data = DB::table('home_infos')->select('id', 'zpid', 'home_id', 'statusType', 'statusText', 'price', 'address', 'imageSrc','beds', 'baths', 'area')->get();
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $module_name = $this->module_name;
                    return ('<button class="btn btn-primary" style="padding: 9px;" onclick="buildingEdit('.$data->id.')" data-toggle="modal" data-target="#myModal_edit_building"><i class="fa fa-edit"></i></button>');
                })
                ->editColumn('imageSrc', function ($info) {
                    return '<img src="' .$info->imageSrc.'" width="100px"/>';
                })
                ->editColumn('statusText', function ($info) {
                    if($info->statusText == 'Active')
                        return '<p style="background-color: rgb(255, 90, 80);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusText .'</p>';
                    else if($info->statusText == 'Sold')
                        return '<p style="background-color: rgb(255, 210, 55);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusText .'</p>';
                    else
                        return '<p style="background-color: rgb(152, 93, 255);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusText .'</p>';
                })
                ->editColumn('statusType', function ($info) {
                    if($info->statusType == 'FOR_SALE')
                        return '<p style="background-color: rgb(255, 90, 80);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusType .'</p>';
                    else if($info->statusType == 'SOLD')
                        return '<p style="background-color: rgb(255, 210, 55);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusType .'</p>';
                    else
                        return '<p style="background-color: rgb(152, 93, 255);padding:2px;border-radius: 30px;text-align: center;font-weight: bold;color: white">'. $info->statusType .'</p>';
                })
                ->rawColumns(['imageSrc', 'action', 'statusType', 'statusText'])
                ->make(true);
        }

    }
    public function buildingEdit(Request $request) {
        $id = $request->get('ID');
        $data = DB::table('home_infos')->where('id', $id)->get();
        return response()->json($data);
    }
    public function buildingStore(Request $request) {
        $id = $request->get('id');
        $home_id = $request->get('home_id');
        $zpid = $request->get('zpid');
        $address = $request->get('address');
        $addressCity = $request->get('addressCity');
        $addressStreet = $request->get('addressStreet');
        $addressState = $request->get('addressState');
        $addressZopcode = $request->get('addressZopcode');
        $statusText = $request->get('statusText');
        $baths = $request->get('baths');
        $beds = $request->get('beds');
        $area = $request->get('area');
        $unformattedPrice = $request->get('unformattedPrice');
        $countryCurrency = $request->get('countryCurrency');
        $detail_url = $request->get('detail_url');
        $imageSrc = $request->get('imageSrc');
        $statusType = '';
        $price = $countryCurrency .number_format($unformattedPrice);
        if($statusText == 'Active') {
            $statusType = 'FOR_SALE';
        }else if($statusText == 'Sold') {
            $statusType = 'SOLD';
        }else if($statusText == 'ForRent') {
            $statusType = 'For Rent';
            $price = $price . '/mo';
        }

        DB::table('home_infos')->where('id', $id)->update(['home_id' => $home_id, 'zpid' => $zpid, 'address' => $address, 'addressCity' => $addressCity,
            'addressStreet' => $addressStreet, 'addressState' => $addressState, 'addressZopcode' => $addressZopcode, 'statusText' => $statusText, 'baths' => $baths,
            'beds' => $beds, 'area' => $area, 'unformattedPrice' => $unformattedPrice, 'countryCurrency' => $countryCurrency, 'detail_url' => $detail_url, 'imageSrc' => $imageSrc,
            'statusType' => $statusType, 'price' => $price]);
        return 'success';
    }

    public function getImage(Request $request) {
        $id = $request->get('ID');

        $images = DB::table('images')->where('address_id', $id)->get();
        return response()->json($images);
    }
    public function settings(Request $request) {

    }
    public function show(Request $request) {
        $type = $request->get('type');
        $content = $request->get('content');
        if($type == 1) {

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
        }else if($type == 2) {
            if($request->get('status') == 'true') {
                DB::table('home_infos')->update(['subfix_word' => $content]);
                DB::update('update home_infos set address = CONCAT(address, "'. $content .'")');
                return 'save_success';
            }elseif($request->get('status') == 'false') {
                $first = DB::table('home_infos')->orderBy('id')->first();
                $search = $first->subfix_word;
                DB::update('update home_infos set address = REPLACE(address,"'. $search .'", "'. $content .'")');
                DB::table('home_infos')->update(['subfix_word' => $content]);
                return 'update_success';
            }
        }
    }


    public function scraping() {
        $response = Http::withHeaders(['accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8', 'accept-encoding' => 'gzip, deflate, br', 'accept-language' => 'en-US,en;q=0.8', 'upgrade-insecure-requests' => '1', 'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951 Safari/537.36'])->get('https://www.zillow.com/homedetails/92-831-Makakilo-Dr-APT-23-Kapolei-HI-96707/61194373_zpid/');
        dd($response); exit();
//        $response_for_sale = Http::get('https://www.zillow.com/search/GetSearchPageState.htm', [
//            'searchQueryState' => '{"pagination":{},"usersSearchTerm":"Kapolei, HI","mapBounds":{"west":-158.31040816308592,"east":-157.85584883691405,"south":21.15340351122823,"north":21.557566262021897},"regionSelection":[{"regionId":45983,"regionType":6}],"isMapVisible":true,"filterState":{"sortSelection":{"value":"globalrelevanceex"},"isAllHomes":{"value":true}},"isListVisible":true,"mapZoom":11}',
//            'wants' => '{"cat1":["listResults","mapResults"],"cat2":["total"],"regionResults":["total"]}',
//            'requestId' => 7
//        ]);
//        $data = $response_for_sale->json();
//        if ($data == null) {sidebar sidebar-dark sidebar-fixed
//            echo 'error';
//        }else {
//            if(count($data['cat1']['searchResults']['listResults']) != 0) {
//                $temp = $data['cat1']['searchResults']['listResults'];
//                $this->insertData($temp);
//                echo 'success';
//            }
//        }

//         Open a chrome browser.

        $capabilities = array(WebDriverCapabilityType::BROWSER_NAME => 'chrome');
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);

        $this->webDriver->get($this->url);

        // Initialize the eyes SDK and set your private API key.
        $eyes = new Eyes();
        $eyes->setApiKey('AXqF51F0uMluNg7hsaZHLMwUqRRTGczJtQB5E4KxXio110');

        try {

            $appName = 'Hello World!';
            $testName = 'My first PHP test!';

            // Start the test and set the browser's viewport size to 800x600
            $eyes->open($this->webDriver, $appName, $testName,
                new RectangleSize(800, 600));

            // Visual checkpoint #1.
            $eyes->checkWindow("Hello!");

            // Click the "Click me!" button
            $this->webDriver->findElement(WebDriverBy::tagName("button"))->click();

            // Visual checkpoint #2.
            $eyes->checkWindow("Click!");

            // End the test.
            $eyes->close();

        } finally {

            // Close the browser.
            $this->webDriver->quit();

            // If the test was aborted before eyes->close was called,
            // ends the test as aborted.
            $eyes->abortIfNotClosed();

        }
    }
    public function insertData($params) {
        foreach ($params as $item) {
            if (DB::table('home_infos')->where('home_id', $item['id'])->doesntExist()) {
                DB::table('home_infos')->insert([
                    'zpid' => array_key_exists("zpid", $item) ? $item['zpid'] : '',
                    'home_id' => array_key_exists("id", $item) ? $item['id'] : '',
                    'providerListingId' =>array_key_exists("providerListingId", $item) ? $item['providerListingId'] : '',
                    'imageSrc' => array_key_exists("imgSrc", $item) ? $item['imgSrc'] : '',
                    'hasImage' => array_key_exists("hasImage", $item) ? $item['hasImage'] : '',
                    'statusType' => array_key_exists("statusType", $item) ? $item['statusType'] : '',
                    'statusText' => array_key_exists("statusText", $item) ? $item['statusText'] : '',
                    'countryCurrency' => array_key_exists("countryCurrency", $item) ? $item['countryCurrency'] : '',
                    'price' => array_key_exists("price", $item) ? $item['price'] : '',
                    'unformattedPrice' => array_key_exists("unformattedPrice", $item) ? $item['unformattedPrice'] : '',
                    'address' => array_key_exists("address", $item) ? $item['address'] : '',
                    'addressStreet' => array_key_exists("addressStreet", $item) ? $item['addressStreet'] : '',
                    'addressCity' => array_key_exists("addressCity", $item) ? $item['addressCity'] : '',
                    'addressState' => array_key_exists("addressState", $item) ? $item['addressState'] : '',
                    'addressZopcode' => array_key_exists("addressZipcode", $item) ? $item['addressZipcode'] : '',
                    'isundisclosedAddress' => array_key_exists("isUndisclosedAddress", $item) ? $item['isUndisclosedAddress'] : '',
                    'beds' => array_key_exists("beds", $item) ? $item['beds'] : 0,
                    'baths' => array_key_exists("baths", $item) ? $item['baths'] : 0.0,
                    'area' => array_key_exists("area", $item) ? $item['area'] : 0,
                    'badegeInfo' => array_key_exists("badgeInfo", $item) ? $item['badgeInfo'] : '',
                    'zestimate' => array_key_exists("zestimate", $item) ? $item['zestimate'] : '',
                    'detail_url' => array_key_exists("detailUrl", $item) ? $item['detailUrl'] : '',
                    'brokerName' => array_key_exists("brokerName", $item) ? $item['brokerName'] : '',
                    'city_id' => 3
                ]);
            }
        }
    }
}
