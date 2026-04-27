<?php

//Permissions Route
Route::get('membership', ['as' => 'membership.view', 'middleware' => ['permission:admin.membership.view'], 'uses' => 'MembershipsController@getIndex']);
Route::get('membership/list', ['as' => 'membership.list', 'middleware' => ['permission:admin.membership.view'], 'uses' => 'MembershipsController@getList']);
Route::get('membership/add', ['as' => 'membership.add', 'middleware' => ['permission:admin.membership.add'], 'uses' => 'MembershipsController@getAdd']);
Route::post('membership/add', ['as' => 'membership.add', 'uses' => 'MembershipsController@postAdd']);
Route::get('membership/edit/{id}', ['as' => 'membership.edit', 'middleware' => ['permission:admin.membership.edit'], 'uses' => 'MembershipsController@getEdit']);
Route::post('membership/edit/{id}', ['as' => 'membership.edit', 'middleware' => ['permission:admin.membership.edit'], 'uses' => 'MembershipsController@postEdit']);
Route::post('membership/delete', ['as' => 'membership.delete', 'middleware' => ['permission:admin.membership.delete'], 'uses' => 'MembershipsController@postDelete']);
Route::post('membership/status', ['as' => 'membership.status', 'middleware' => ['permission:admin.membership.status'], 'uses' => 'MembershipsController@postStatus']);
