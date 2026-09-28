<?php

use App\Http\Controllers\Api\CallbackController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::post('/', [TransactionController::class, 'store']);
    Route::get('/{transaction}', [TransactionController::class, 'show']);
});

Route::post('/callbacks', [CallbackController::class, 'store']);

Route::post(
    '/callbacks/generate-signature',
    [CallbackController::class, 'generateSignature']
);
