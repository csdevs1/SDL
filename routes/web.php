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

Route::get('/', function () {
    return view('reports.graph');
});
Route::get('/dashboard/licence', function () {
    return view('home');
});
Route::match(array('GET', 'POST'),'/deliveries/deliveries/', function () {
    return view('deliveries.deliveries');
});

Route::match(array('GET', 'POST'),'/reports/performance-plate', function () {
    return view('reports.performance_plate');
});
Route::match(array('GET', 'POST'),'/reports/performance-driver', function () {
    return view('reports.performance_driver');
});
Route::get('/reports/get/performance-plate', 'ReportController@GetDispatchReport');
Route::get('/reports/get/performance-driver', 'ReportController@GetDispatchReportByEmployee');
/*
Route::get('/documents', function () {
    return view('upload.upload'); // Upload document page view
});*/
Route::get('/upload/documents', 'StatisticsController@index'); // Maintainer vehicle
Route::get('/vehicle/route', 'DeliveryController@vehicleRoute');

Route::get('/reports/summary', function () {
    return view('reports.graph'); // Reports -> SDL Summary page view
});Route::get('/home', function () {
    return view('reports.graph'); // Reports -> SDL Summary page view
});
//Route::get('/home', 'HomeController@index')->name('home');
// VEHICLE
    Route::get('/maintainers/vehicles/', 'VehiclesController@create'); // Maintainer vehicle
    Route::post('/vehicle/save', 'VehiclesController@save'); // Maintainer vehicle
    Route::post('/vehicle/update', 'VehiclesController@update'); // Maintainer vehicle
    Route::post('/vehicle/delete', 'VehiclesController@delete'); // Maintainer vehicle
// VEHICLE

// VEHICLE GROUP
    Route::get('/maintainers/vehicles-groups', 'VehiclesController@listGroup'); // Maintainer office
    Route::post('/maintainers/create/vehicle_groups',      ['as' => 'vehicle.create-group',        'uses' => 'VehiclesController@storeGroup']);
    Route::post('/vehicle/group/delete', 'VehiclesController@delete_group'); // Maintainer vehicle
    Route::post('/vehicle/group/update', 'VehiclesController@update_group'); // Maintainer vehicle
// VEHICLE GROUP

// DEVICES
    Route::get('/maintainers/devices/', 'DevicesController@show'); // Maintainer vehicle
    Route::post('/devices/save', 'DevicesController@save'); // Maintainer vehicle
    Route::post('/devices/update', 'DevicesController@update'); // Maintainer vehicle
    Route::post('/device/delete', 'DevicesController@delete'); // Maintainer vehicle
// DEVICES

// GROUP OFFICE
    Route::get('/maintainers/office-groups', 'OfficesController@getGroup'); // Maintainer office
    Route::post('/maintainers/create/office_groups',       ['as' => 'office.create-group',         'uses' => 'OfficesController@storeGroup']);
    Route::post('/maintainers/update/office_groups',       ['as' => 'office.update-group',         'uses' => 'OfficesController@update']);
    Route::post('/maintainers/delete/office_groups',       ['as' => 'office.delete-group',         'uses' => 'OfficesController@delete']);
    Route::get('/get/offices/group-office-id',       ['as' => 'office.goup-office',         'uses' => 'OfficesController@getOfficeByOfficeGroupId']);
// GROUP OFFICE

// OFFICE
    Route::get('/maintainers/offices',                ['as' => 'offices.list',                'uses' => 'OfficesController@getOffices']);
    Route::post('/maintainers/create/offices',             ['as' => 'office.create',               'uses' => 'OfficesController@store']);
    Route::post('/maintainers/update/office',       ['as' => 'office.update',         'uses' => 'OfficesController@update_office']);
    Route::post('/maintainers/delete/office',       ['as' => 'office.delete',         'uses' => 'OfficesController@delete_office']);
// OFFICE

// PROFILES
    Route::get('/maintainers/profiles',                ['as' => 'profiles.list',                'uses' => 'ProfileController@index']);
    Route::get('/maintainers/profiles/permission/{id}',                ['as' => 'profiles.permission',                'uses' => 'ProfileController@permissionList']);
    Route::post('/maintainers/create/profile',                ['as' => 'profiles.create',                'uses' => 'ProfileController@create']);
    Route::post('/maintainers/profile/delete',                ['as' => 'profiles.delete',                'uses' => 'ProfileController@delete']);
    Route::post('/maintainers/profile/update',                ['as' => 'profiles.update',                'uses' => 'ProfileController@update']);
    Route::post('/maintainers/create/profile/permission',                ['as' => 'profiles.create',                'uses' => 'ProfileController@create_permission']);
// PROFILES

// USERS MAINTAINER
    Route::get('/maintainers/users/',                ['as' => 'users.list',                'uses' => 'UsersController@index']);
    Route::post('/user/create',                ['as' => 'user.create',                'uses' => 'UsersController@create']);
    Route::post('/user/delete',                ['as' => 'user.delete',                'uses' => 'UsersController@delete']);
    Route::post('/user/update',                ['as' => 'usermaintainers.update',                'uses' => 'UsersController@update']);
// USERS MAINTAINER

// REJECT MAINTAINER
    Route::get('/maintainers/status/',                ['as' => 'status.list',                'uses' => 'StatusController@index']);
    Route::post('/status/create',                ['as' => 'status.create',                'uses' => 'StatusController@create']);
    Route::post('/status/update/access',                ['as' => 'status.update',                'uses' => 'StatusController@update_access']);
// REJECT MAINTAINER

// SELLERS MAINTAINER
    Route::get('/maintainers/sellers/',                ['as' => 'seller.list',                'uses' => 'SellersController@index']);
    Route::post('/seller/create',                ['as' => 'seller.create',                'uses' => 'SellersController@create']);
    Route::get('/sellers/get/clients/id',                ['as' => 'seller.clients',                'uses' => 'SellersController@get_clients_id']);
    Route::get('/sellers/get/clients',                ['as' => 'seller.clients',                'uses' => 'SellersController@get_clients']);
    Route::post('/maintainers/sellers/associate/clients',                ['as' => 'seller.associate.clients',                'uses' => 'SellersController@associate_customer']);
// SELLERS MAINTAINER

// EMPLOYEES MANTAINER
    Route::get('/maintainers/employees/',                ['as' => 'employees.list',                'uses' => 'EmployeeController@index']);
    Route::post('/maintainers/employees/create',                ['as' => 'employees.create',                'uses' => 'EmployeeController@create']);
    Route::post('/maintainers/employees/update',                ['as' => 'employees.update',                'uses' => 'EmployeeController@update']);
    Route::post('/maintainers/employees/delete',                ['as' => 'employees.delete',                'uses' => 'EmployeeController@delete']);
// EMPLOYEES MANTAINER


Route::get('/get/vehicles/office-id',       ['as' => 'vehicles.vehicles-group',         'uses' => 'VehiclesController@get_vehicles_by_office']);
Route::get('/get/vehicles/vehicle-group-id', 'VehiclesController@get_vehicles_by_group'); // Maintainer office
Route::get('/get/permimssions/id', 'ProfileController@get_permissions');

Route::get('/documents/rejected/reason', 'ReportController@RejectsByReason');
Route::get('/documents/rejected/partial', 'ReportController@PartialRejectsByReason');
Route::get('/report/historic_tendency', 'ReportController@HistoricTendency');
Route::get('/report/kpi_indicator', 'ReportController@KpiIndicator');
Route::get('/deliveries/channel', 'DeliveryController@ChannelDeliverybyDate');

Route::post('/file/upload', 'Upload@importFile');
Route::get('/document/list/',                 ['as' => 'document.list',                'uses' => 'DocumentsController@ListDocuments']);
Route::get('/get/document/', ['as' => 'delivery.document', 'uses' => 'DeliveryController@document']);
Route::get('/get/documents', 'DocumentsController@getDocument');
Route::get('/get/documents/status', 'DocumentsController@getDocumentByStatus');
Route::get('/dashboard/list/deliveries', ['as' => 'delivery.dashboard', 'uses' => 'DeliveryController@DashBoard']);
Route::get('/statistic/licence/', ['as' => 'statistic.licence', 'uses' => 'StatisticsController@licence']);

Route::get('/document/attachment',             ['as' => 'delivery.attatchment',               'uses' => 'DeliveryController@Document_Attachment']);
Route::get('/document/signature',             ['as' => 'delivery.signature',               'uses' => 'DeliveryController@Document_Sign']);
Route::get('/deliveries/vehicle/list/vehicle/{plate}/{delivery_date}',             ['as' => 'delivery.vehicle',               'uses' => 'DeliveryController@Vehicle']);
Route::get('/image/letter',            ['as' => 'delivery.icon',               'uses' => 'DeliveryController@getIconImage']);
Route::get('/update/gauge',            ['as' => 'delivery.updateGauge',               'uses' => 'DeliveryController@updateGauge']);

// USER AUTH
Auth::routes();
Route::get('/profile', 'UsersController@user_profile');
// USER AUTH
