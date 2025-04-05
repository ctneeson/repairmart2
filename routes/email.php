<?php

use App\Http\Controllers\EmailController;


// AUTH: User must be logged in
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/email/create', [EmailController::class, 'create'])->name('email.create');
    Route::post('/email', [EmailController::class, 'store'])->name('email.store');
    Route::get('/email', [EmailController::class, 'index'])->name('email.index');
    Route::get('/email/{message}', [EmailController::class, 'show'])->name('email.show');
    Route::get('/email/{email}/reply', [EmailController::class, 'reply'])->name('email.reply');
    Route::patch('/email/{email}/mark-unread', [EmailController::class, 'markUnread'])->name('email.mark-unread');
    Route::delete('/email/{email}', [EmailController::class, 'destroy'])->name('email.destroy');
    Route::get(
        '/email/attachment/{attachment}/download',
        [EmailController::class, 'downloadAttachment']
    )->name('email.attachment.download');

});