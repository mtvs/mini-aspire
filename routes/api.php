<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('officer')
    ->middleware(['auth:api', function($request, $next) {
        if (! auth()->user()->is_officer) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Only accessible by officers');
        }

        return $next($request);
    }])
    ->namespace('Officer')
    ->group(function() {
        Route::post('loans', 'LoansController@store');
    });

Route::prefix('customer')
    ->namespace('Customer')
    ->group(function () {
        Route::post('repayment/{repayment}/claim', 'RepaymentsController@claim');
    });