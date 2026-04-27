<?php

//Partners Route
Route::get('partners', ['as' => 'partners.view', 'middleware' => ['permission:admin.partners.view'], 'uses' => 'PartnersController@getIndex']);
Route::get('partners/list', ['as' => 'partners.list', 'middleware' => ['permission:admin.partners.view'], 'uses' => 'PartnersController@getList']);
Route::get('partners/add', ['as' => 'partners.add', 'middleware' => ['permission:admin.partners.add'], 'uses' => 'PartnersController@getAdd']);
Route::post('partners/add', ['as' => 'partners.add', 'uses' => 'PartnersController@postAdd']);
Route::get('partners/edit/{id}', ['as' => 'partners.edit','middleware' => ['permission:admin.partners.edit'], 'uses' => 'PartnersController@getEdit']); 
Route::post('partners/edit/{id}', ['as' => 'partners.edit', 'middleware' => ['permission:admin.partners.edit'],'uses' => 'PartnersController@postEdit']); 
Route::post('partners/delete', ['as' => 'partners.delete', 'middleware' => ['permission:admin.partners.delete'],'uses' => 'PartnersController@postDelete']); 
Route::post('partners/status', ['as' => 'partners.status','middleware' => ['permission:admin.partners.status'], 'uses' => 'PartnersController@postStatus']); 
Route::get('partners/permissions/{id}', ['as' => 'partners.permissions', 'middleware' => ['permission:admin.partners.permissions'], 'uses' => 'PartnersController@getPermissions']); 
Route::post('partners/permissions/{id}', ['as' => 'partners.permissions','middleware' => ['permission:admin.partners.permissions'], 'uses' => 'PartnersController@postPermissions']); 