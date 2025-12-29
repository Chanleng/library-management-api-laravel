<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use GuzzleHttp\Promise\Create;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage as FacadesStorage;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::with('author');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('isbn', 'LIKE', "%{$search}%")
                    ->orWhereHas('author', function ($authorQuery) use ($search) {
                        $authorQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }
        if ($request->has('genre')) {
            $query->where('genre', $request->genre);
        }
        $books = $query->orderBy('id', 'desc')->paginate(10);
        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {

        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');

            // Generate a unique filename
            $filename = time() . '.' . $image->getClientOriginalExtension();

            // Move the file to public/covers folder
            $image->move(public_path('covers'), $filename);

            // Save the URL path for frontend
            $data['cover_image'] = url('covers/' . $filename);
        } else {
            // If no file uploaded, set to null so DB default is used
            unset($data['cover_image']);
        }


        $book = Book::create($data);
        $book->load('author');
        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //

        $book->load('author');
        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBookRequest $request, Book $book)
    {
        //
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');

            // Generate a unique filename
            $filename = time() . '.' . $image->getClientOriginalExtension();

            // Move the file to public/covers folder
            $image->move(public_path('covers'), $filename);

            // Save the URL path for frontend
            $data['cover_image'] = url('covers/' . $filename);
        } else {
            // If no file uploaded, set to null so DB default is used
            unset($data['cover_image']);
        }
        $book->update($data);
        $book->load('author');
        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json([
            "status" => true,
            'message' => "Book deleted successfully"
        ]);
    }
}
