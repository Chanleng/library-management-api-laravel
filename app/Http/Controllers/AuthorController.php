<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Controllers\update;
use App\Models\Book;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = Author::with('books');
        if($request->has('search')){
            $search = $request->search;
            $query->where(function($e) use ($search){
                $e->where('name', 'LIKE', "%{$search}%");
            });
        }
        $authors =$query->orderBy('id', 'desc')->paginate(10);
        return AuthorResource::collection($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request)
    {
        //
        $author = Author::create($request->validated());
        return new AuthorResource($author);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $author = Author::findOrFail($id);
        return new AuthorResource($author);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreAuthorRequest $request, Author $author)
    {
        //
        $author->update($request->validated());
        return new AuthorResource($author);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        //
        $author->delete();
        
        return response()->noContent();
    }
}
