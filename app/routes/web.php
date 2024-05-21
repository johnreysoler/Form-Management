<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/form/create/{id}',[App\Http\Controllers\FormController::class, 'create']);
Route::get('/form/edit/{id}',[App\Http\Controllers\FormController::class, 'edit']);
Route::get('/form/index',[App\Http\Controllers\FormController::class, 'index'])->name('form_list');
Route::get('/form/search',[App\Http\Controllers\FormController::class, 'show']);
Route::patch('/form/publish/{formid}',[App\Http\Controllers\FormController::class, 'update']);
Route::get('/form/{user_id}',[App\Http\Controllers\ResponseController::class, 'index'])->name('userForm');
Route::get('/optiontype', [App\Http\Controllers\OptionTypeController::class, 'index']);
Route::get('/fetchOptions',[App\Http\Controllers\FormController::class, 'fetchOptions']);
Route::get('/fetchPrerequisiteQuestion/{form_id}',[App\Http\Controllers\FormSectionController::class, 'show']);
Route::get('/fetchPrerequisiteOption/{question_id}',[App\Http\Controllers\OptionController::class, 'show']);

Route::get('/assignment/{assignment_id}',[App\Http\Controllers\AssignmentController::class, 'show']);

Route::post('/form/store',[App\Http\Controllers\FormController::class, 'store']);
Route::post('/form/section/store',[App\Http\Controllers\FormSectionController::class, 'store']);
Route::get('/form/user/{form_id}',[App\Http\Controllers\FormController::class, 'fetchUserForm']);
Route::post('/form/user/response',[App\Http\Controllers\ResponseController::class, 'store']);

