<?php

/*
*
* Frontend Routes
*
* --------------------------------------------------------------------
*/
Route::group(['namespace' => '\Modules\Article\Http\Controllers\Frontend', 'as' => 'frontend.', 'middleware' => 'web', 'prefix' => ''], function () {

    /*
     *
     *  Posts Routes
     *
     * ---------------------------------------------------------------------
     */
    $module_name = 'posts';
    $controller_name = 'PostsController';
    Route::get("$module_name", ['as' => "$module_name.index", 'uses' => "$controller_name@index"]);
    Route::get("$module_name/{id}/{slug?}", ['as' => "$module_name.show", 'uses' => "$controller_name@show"]);

    /*
     *
     *  Categories Routes
     *
     * ---------------------------------------------------------------------
     */
    $module_name = 'categories';
    $controller_name = 'CategoriesController';
    Route::get("$module_name", ['as' => "$module_name.index", 'uses' => "$controller_name@index"]);
    Route::get("$module_name/{id}/{slug?}", ['as' => "$module_name.show", 'uses' => "$controller_name@show"]);
});

/*
*
* Backend Routes
*
* --------------------------------------------------------------------
*/
Route::group(['namespace' => '\Modules\Article\Http\Controllers\Backend', 'as' => 'backend.', 'middleware' => ['web', 'auth', 'can:view_backend'], 'prefix' => 'admin'], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Posts Routes
     *
     * ---------------------------------------------------------------------
     */
    $module_name = 'posts';
    $controller_name = 'PostsController';


    Route::get("$module_name/scanning", ['as' => "$module_name.scanning", 'uses' => "$controller_name@scanning"]);

    Route::get("$module_name/index_list", ['as' => "$module_name.index_list", 'uses' => "$controller_name@index_list"]);
    Route::get("$module_name/index_data", ['as' => "$module_name.index_data", 'uses' => "$controller_name@index_data"]);
    Route::get("$module_name/trashed", ['as' => "$module_name.trashed", 'uses' => "$controller_name@trashed"]);
    Route::patch("$module_name/trashed/{id}", ['as' => "$module_name.restore", 'uses' => "$controller_name@restore"]);
    Route::resource("$module_name", "$controller_name");
    /*
     *
     *  Categories Routes
     *
     * ---------------------------------------------------------------------
     */
    $module_name = 'categories';
    $controller_name = 'CategoriesController';
    Route::get("$module_name/index_list", ['as' => "$module_name.index_list", 'uses' => "$controller_name@index_list"]);
    Route::get("$module_name/index_data", ['as' => "$module_name.index_data", 'uses' => "$controller_name@index_data"]);
    Route::get("$module_name/trashed", ['as' => "$module_name.trashed", 'uses' => "$controller_name@trashed"]);
    Route::patch("$module_name/trashed/{id}", ['as' => "$module_name.restore", 'uses' => "$controller_name@restore"]);
    Route::resource("$module_name", "$controller_name");

    $module_name = 'buildings';
    $controller_name = 'BuildingsController';
    Route::get("$module_name/buildingStore", ['as' => "$module_name.buildingStore", 'uses' => "$controller_name@buildingStore"]);
    Route::get("$module_name/buildingEdit", ['as' => "$module_name.buildingEdit", 'uses' => "$controller_name@buildingEdit"]);
    Route::post("$module_name/settings", ['as' => "$module_name.settings", 'uses' => "$controller_name@settings"]);
    Route::get("$module_name/getImage", ['as' => "$module_name.getImage", 'uses' => "$controller_name@getImage"]);
    Route::get("$module_name/scraping", ['as' => "$module_name.scraping", 'uses' => "$controller_name@scraping"]);
    Route::get("$module_name/building_get", ['as' => "$module_name.building_get", 'uses' => "$controller_name@building_get"]);
    Route::resource("$module_name", "$controller_name");

});
