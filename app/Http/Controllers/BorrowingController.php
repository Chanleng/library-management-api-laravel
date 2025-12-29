<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\Book;
use App\Models\Borrowing;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrowings = Borrowing::with('book', 'member')->orderBy('id', 'desc')->paginate(10);
        return BorrowingResource::collection($borrowings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingRequest $request)
    {
        $book = Book::findOrFail($request->book_id);
        if (!$book->isAvailable()) {
            return response()->json([
                'status' => false,
                'message' => 'book is not available for borrowing'
            ]);
        }
        $borrowing = Borrowing::create($request->validated());
        $book->borrow();
        return new BorrowingResource($borrowing);
    }


    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['book', 'member']);
        return new BorrowingResource($borrowing);
    }
    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'borrowed' && $borrowing->status !== 'overdue') {
            return response()->json([
                'status' => false,
                'message' => 'Book has already been returned'
            ]);
        }
        $borrowing->update([
            'returned_date' => now(),
            'status' => 'returned'
        ]);
        $borrowing->book->returnedBook();
        $borrowing->load(['book', 'member']);
        return new BorrowingResource($borrowing);
    }
    public function overDueDate()
    {
        $borrowings = Borrowing::with(['book', 'member'])->where('status', 'borrowed')->get();
        foreach ($borrowings as $borrowing) {
            if ($borrowing->isOverdue()) {
                $borrowing->update([
                    'status' => 'overdue'
                ]);
            }
        }
        $isOverDueDate = Borrowing::with(['book', 'member'])->where('status', 'overdue')->get();
        return BorrowingResource::collection($isOverDueDate);
    }
}
