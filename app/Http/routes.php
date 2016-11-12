<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::get('/admin_login', 'AdminController@adminLoginGet');
Route::post('/admin_login', 'AdminController@adminLoginPost');
Route::get('/',  function (){
    return Redirect('/admin_login');
});
Route::get('/admin_logout', 'AdminController@userLogout');


Route::get('/admin_dashboard', 'AdminController@adminDashGet');
Route::get('/add_emp', 'AdminController@addEmployee');
Route::post('/add_emp', 'AdminController@addEmployeePost');
Route::get('/emp_del', 'AdminController@deleteEmployees');
Route::get('/emp_detail', 'AdminController@getProfileView');

Route::get('/update_emp', 'AdminController@getUpdateEmp');
Route::post('/update_emp', 'AdminController@postUpdateEmp');




Route::get('/popuser', 'AdminController@populateUser');

Route::get('/test','AdminController@test');



//user routes


Route::get('/user_login','UserController@getLoginPage');
Route::post('/user_login','UserController@loginPost');
Route::get('/user_logout','UserController@userLogout');


Route::get('/user_dash','UserController@getDashUser');
Route::get('/request_leave','UserController@getLeaveReq');






Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
