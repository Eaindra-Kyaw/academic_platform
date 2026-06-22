<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MLPredictionController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/ml/predict', [MLPredictionController::class, 'predictStudent']);
    Route::get('/ml/batch', [MLPredictionController::class, 'batchPredict']);
    Route::get('/ml/metrics', [MLPredictionController::class, 'modelMetrics']);
});
