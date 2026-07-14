<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| All web (browser) routes are handled here. Inertia renders a React page
| from the resources/js/Pages directory.
|
*/

Route::inertia('/', 'Dashboard');
Route::inertia('/tickets', 'Tickets');
Route::inertia('/login', 'Login');

Route::get('/tickets/{id}', function ($id) {
    return Inertia::render('TicketDetail', [
        'ticketId' => (int) $id,
    ]);
});

