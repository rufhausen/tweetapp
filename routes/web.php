<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Services\TwitterApi;
use App\TwitterUser;

//Auth::routes();

Route::get('test', function()
{

});

Route::get('/', 'HomeController@index')->name('home');
Route::post('mentions', 'HomeController@postMentions');
Route::get('profile/{twitter_handle?}', 'HomeController@getProfile');

//Route::get('profile/{twitter_handle?}', function($twitterHandle)
//{
//    //return view('profile');
//    $response = $this->twitterApi->getUserByScreenName($twitterHandle);
//    return $response;
//    //return json_encode($response);
//});



