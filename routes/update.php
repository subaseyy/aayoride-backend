<?php
/*
|--------------------------------------------------------------------------
| Update Routes
|--------------------------------------------------------------------------
|
| This route is responsible for handling the updating process
|
|
|
*/

use App\Http\Controllers\UpdateController;
use Illuminate\Support\Facades\Route;

Route::any('/', [UpdateController::class, 'update_software_index'])->name('index');
Route::any('update-system', [UpdateController::class, 'update_software'])->name('update-system');

Route::fallback(function () {
    return redirect('/');
});
