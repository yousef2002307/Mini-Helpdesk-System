<?php

use App\Http\Controllers\User\ReplyController;
use App\Http\Controllers\User\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/tickets', [TicketController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
Route::post('/tickets/{ticket}/replies', [ReplyController::class, 'store']);
