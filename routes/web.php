<?php

use Illuminate\Support\Facades\Auth;

Auth::routes();

use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ProcessAddRequest;


Route::get('/index', function () {
    return view('admin.module.index.index');
});

Route::post('logout', [
    'as' => 'logout',
    'Auth\LogoutController@postLogout']);

Route::post('testup',function(){
	$file = request()->file('myFile');
	$destinationPath = 'uploads';
    $file->move($destinationPath,$file->getClientOriginalName());
});
//cong list route cam pha'
Route::get('/languages/{locale}', [
    'as'=> 'setlanguaes',
    'uses'=>'LanguageController@index'
]);

Route::get('/login', [
    'as' => 'login',
    'uses' => 'Auth\LoginController@getLogin'
]);

Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

Route::post('/login', [
    'as' => 'login',
    'uses' => 'Auth\LoginController@login',
]);

Route::get('/register ', 'Auth\RegisterController@getRegister')->name('getRegister');

Route::post('/register', [
    'as' => 'register-user',
    'uses' => 'Auth\RegisterController@register',
]);

Route::group(['prefix' => 'employee', 'middleware' => 'user'], function () {


});

Route::group(['middleware' => 'user'], function () {

    Route::get('/', [
        'as' => 'welcome',
        'uses' => 'User\DashboardController@index',
    ]);

    Route::get('/dashboard', [
        'as' => 'dashboard-user',
        'uses' => 'User\DashboardController@index',
    ]);
    Route::post('employee/postFile', 'User\Employee\EmployeeController@postFile')->name('postFile');
    Route::get('employee/importEmployee', 'User\Employee\EmployeeController@importEmployee')->name('importEmployee');
    Route::post('employee/edit-password', 'User\Employee\EmployeeController@editPass')->name('editPass');
    Route::resource('employee', 'User\Employee\EmployeeController');
    Route::post('/employee/{id}', [
        'as' => 'show_chart',
        'uses' => 'User\Employee\EmployeeController@showChart',
    ]);

    Route::get('/export', 'User\Employee\EmployeeController@export')->name('export');


    Route::resource('teams', 'Team\TeamController');
    Route::get('checkTeamNameEdit', 'Team\TeamController@checkNameTeam');
    Route::post('teams/chart', 'Team\TeamController@showChart');

    Route::post('vendors/postFile', 'User\Vendor\VendorController@postFile')->name('postFile');
    Route::get('vendors/importVendor', 'User\Vendor\VendorController@importVendor')->name('importVendor');
    Route::get('/vendors/export', 'User\Vendor\VendorController@export')->name('vendor-export');
    Route::post('vendors/edit-password', 'User\Vendor\VendorController@editPass')->name('editPass');
    Route::resource('vendors', 'User\Vendor\VendorController');
    Route::resource('projects', 'Project\ProjectController');
    Route::get('absences/hr',[
        'uses'=>'Absence\AbsenceController@indexHR',
        'as'=>'absences-hr'
    ]);
    Route::post('absences/hr/export',[
        'uses'=>'Absence\AbsenceController@exportAbsenceHR',
        'as'=>'export-absences-hr'
    ]);

    Route::resource('absences', 'Absence\AbsenceController');

    Route::post('projects/checkProcessAjax',[
        'as'=>'checkProcessAjax',
        'uses'=>'Project\ProjectController@checkProcessesAjax'
    ]);
    Route::post('projects/reopenProjectAjax',[
        'as'=>'reopenProjectAjax',
        'uses'=>'Project\ProjectController@reopenProjectAjax'
    ]);



    Route::post('/vendors/{id}', [
        'as' => 'vendor_show_chart',
        'uses' => 'User\Vendor\VendorController@showChart',
    ]);

    Route::get('absence/po-project/{id}', 'Absence\AbsenceController@confirmRequest')->name('confirmRequest');
    Route::post('absence/po-project/{id}', 'Absence\AbsenceController@confirmRequestAjax')->name('confirmRequestAjax');


    Route::get('/absence-po', 'Absence\AbsenceController@showListAbsence')->name('absence-po');
    Route::post('/deny-po-team', 'Absence\AbsenceController@denyPOTeam');
    Route::post('/done-confirm', 'Absence\AbsenceController@doneConfirm');
});

//cong list route cam pha'

Route::post('logout', [
    'as' => 'logout',
    'Auth\LogoutController@postLogout']);

/*Route::get('/employee/add',['as' => 'getEmployeeAdd', 'uses' => 'Admin\EmployeeController@getEmployeeAdd']);
Route::post('/employee/add',['as' => 'postEmployeeAdd', 'uses' => 'Admin\EmployeeController@postEmployeeAdd']);
Route::get('/employee/edit/{id}',['as' => 'getEmployeeEdit', 'uses' => 'Admin\EmployeeController@getEmployeeEdit']);
Route::post('/employee/edit/{id}',['as' => 'postEmployeeEdit', 'uses' => 'Admin\EmployeeController@postEmployeeEdit']); */

/*begin route list employee by Quy*/
Route::get('/quy-test', function () {
    return view('teams.test.quy_test');
});
Route::get('/phu-test', function (){
    return view('projects.add');
});
Route::get('/download-template', 'User\Employee\EmployeeController@downloadTemplate');
Route::get('/download-template-vendor', 'User\Vendor\VendorController@downloadTemplateVendor')->name('vendor-template');
/*the end route list employee by Quy*/

//Route::DELETE('employee/{id} ', 'User\Employee\EmployeeController@destroy')->name('remove');

