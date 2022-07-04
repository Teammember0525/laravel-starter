<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use function Symfony\Component\String\length;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
           $this->loading();
        })->everyFiveMinutes();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    public function loading() {
        $remember_id = DB::table('home_infos')->where('id', 1)->first();

        for($i=$remember_id->info2string; $i<60; $i++) {
            $response_for_sale = Http::get('https://www.zillow.com/search/GetSearchPageState.htm', [
                'searchQueryState' => '{"pagination":{"currentPage":'. $i .'},"mapBounds":{"west":-98.36936268164062,"east":-97.20206531835937,"south":29.91852065901562,"north":30.667921672520016},"regionSelection":[{"regionId":10221,"regionType":6}],"isMapVisible":false,"filterState":{"isAllHomes":{"value":true},"sortSelection":{"value":"globalrelevanceex"}},"isListVisible":true}',
                'wants' => '{"cat1":["listResults","mapResults"],"cat2":["total"],"regionResults":["total"]}',
                'requestId' => $i
            ]);
//             $reponse_for_rent = Http::get('https://www.zillow.com/search/GetSearchPageState.htm', [
//                 'searchQueryState' => '{"pagination":{"currentPage":'. $i .'},"mapBounds":{"west":-98.22585377050781,"east":-97.34557422949219,"south":29.918520659015634,"north":30.667921672520016},"regionSelection":[{"regionId":10221,"regionType":6}],"isMapVisible":true,"filterState":{"isAllHomes":{"value":true},"isForSaleByAgent":{"value":false},"isForSaleByOwner":{"value":false},"isNewConstruction":{"value":false},"isComingSoon":{"value":false},"isAuction":{"value":false},"isForSaleForeclosure":{"value":false},"isForRent":{"value":true}},"isListVisible":true}',
//                 'wants' => '{"cat1":["listResults","mapResults"]}',
//                 'requestId' => $i
//             ]);
//             $reponse_for_sold = Http::get('https://www.zillow.com/search/GetSearchPageState.htm', [
//                 'searchQueryState' => '{"pagination":{"currentPage":'. $i .'},"mapBounds":{"west":-98.22585377050781,"east":-97.34557422949219,"south":29.918520659015634,"north":30.667921672520016},"regionSelection":[{"regionId":10221,"regionType":6}],"isMapVisible":true,"filterState":{"isAllHomes":{"value":true},"isForSaleByAgent":{"value":false},"isForSaleByOwner":{"value":false},"isNewConstruction":{"value":false},"isComingSoon":{"value":false},"isAuction":{"value":false},"isForSaleForeclosure":{"value":false},"isRecentlySold":{"value":true},"sortSelection":{"value":"globalrelevanceex"}},"isListVisible":true}',
//                 'wants' => '{"cat1":["listResults","mapResults"]}',
//                 'requestId' => $i
//             ]);
            $data = $response_for_sale->json();
//             $data_rent = $reponse_for_rent->json();
//             $data_sold = $reponse_for_sold->json();
            if ($data == null) {
                $this->remember = $i;
                DB::table('home_infos')->where('id', 1)->update([
                    'info2string' => $this->remember,
                ]);
                break;
            }else {
//                if(count($data['cat1']['searchResults']['listResults']) != 0) {
//                    $temp = $data['cat1']['searchResults']['listResults'];
//                    $this->insertData($temp);
//                 $temp1 = $data_rent['cat1']['searchResults']['listResults'];
//                 $this->insertData($temp1);
//                 $temp2 = $data_sold['cat1']['searchResults']['listResults'];
//                 $this->insertData($temp2);
//                }
                if(count($data['cat1']['searchResults']['mapResults']) != 0) {
                    $temp_map = $data['cat1']['searchResults']['mapResults'];
                    $this->insertData($temp_map);
                    echo count($data['cat1']['searchResults']['mapResults']);
                }

            }

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
                ]);
            }
        }
    }

}
