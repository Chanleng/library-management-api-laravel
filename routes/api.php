<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\MemberController;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('authors', AuthorController::class);
    Route::apiResource('books', BookController::class);
    Route::apiResource('members', MemberController::class);
    Route::apiResource('borrowings', BorrowingController::class);
    //return book
    Route::put('return-book/{borrowing}', [BorrowingController::class, 'returnBook']);
    //over due date
    Route::get('over-due', [BorrowingController::class, 'overDueDate']);
    //Count books by genre
    Route::get('genre-statistics', function () {
        $stats = Book::select('genre', DB::raw('count(*) as total'))->groupBy('genre')->get();
        return response()->json([
            'data' => $stats
        ]);
    });
    //statistics library
    Route::get('statistic-dashboard', function () {
        return response()->json([
            'total_books' => Book::count(),
            'total_members' => Member::count(),
            'borrowed_books' => Borrowing::where('status', 'borrowed')->count(),
            'overdue_books' => Borrowing::where('status', 'overdue')->count()
        ]);
    });
});
