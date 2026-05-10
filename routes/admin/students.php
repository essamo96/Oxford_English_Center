<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StudentsController;

Route::prefix('admin')->middleware(['auth:admin', 'verified'])->group(function () {
    
    // الرسائل البريدية
    Route::post('send-message', [StudentsController::class, 'SendMessage'])->name('send.message');
    Route::post('send-custom-email', [StudentsController::class, 'sendCustomEmail'])->name('send.custom.email');
    Route::post('send-cemail', [StudentsController::class, 'sendCEmail'])->name('send.CEmail');
    
    // باقي الـ routes الخاصة بـ students
    Route::get('students', [StudentsController::class, 'getIndex'])->name('students.view');
    Route::post('students-list', [StudentsController::class, 'getList'])->name('students.list');
    Route::post('students-delete', [StudentsController::class, 'delete'])->name('students.delete');
    Route::post('students-add', [StudentsController::class, 'add'])->name('students.add');
    Route::post('students/details', [StudentsController::class, 'getStudentDetails'])->name('students.details');

    
});
