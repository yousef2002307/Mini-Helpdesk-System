<?php

use App\Http\Controllers\Admin\ReplyController;
use App\Http\Controllers\Admin\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
Route::patch('/tickets/{ticket}', [TicketController::class, 'update']);
Route::post('/tickets/{ticket}/replies', [ReplyController::class, 'store']);
