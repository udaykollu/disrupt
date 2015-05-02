<?php

//Route::get('adduser/{personName}/{userid}/{phone}',array('as' => '', 'uses' => 'HomeController@adduser'));
Route::get('home/{userid}', 'HomeController@home');
Route::get('updatelocation/{userid}/{latitude}/{longitude}/{activity}/{type}', 'HomeController@updatelocation');
Route::get('askhelp/{userid}', 'HomeController@askhelp');
Route::get('adduser/{userid}/{username}/{password}/{phone}', 'HomeController@adduser');
Route::get('verify/{userid}/{accesscode}', 'HomeController@verify');
Route::get('verificationcode/{userid}', 'HomeController@verificationcode');
Route::get('updategcm/{userid}/{gcm}','HomeController@updategcm');
Route::get('login/{userid}/{password}','HomeController@login');
Route::get('forgotpassword/{userid}','HomeController@forgotpassword');//it sends a new verification code
Route::get('resetpassword/{userid}/{oldpassword}/{newpassword}','HomeController@resetpassword');//when user wants to change his/her password
Route::get('updatepassword/{userid}/{password}','HomeController@updatepassword');//user forgot passwrd, verfication code generated using forgot password api
Route::get('trackvictim/{helperuserid}/{victimuserid}','HomeController@trackvictim');
Route::get('helped/{helperuserid}','HomeController@helped');//helper notifies that he helped victim or he no longer want to help
Route::get('helpreceived/{useridd}', 'HomeController@helpreceived');//victim notifies help received
Route::get('feedback/{userid}/{report}', 'HomeController@feedback');
Route::get('helperlist/{useridd}', 'HomeController@helperlist');
/*
Route::get('/', function()
{
	return View::make('hello');
});
*/
?>

