<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $books = Book::query()
            ->with('genres')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(10);

        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genres = Genre::orderBy('name')->get();

        return view('books.create', compact('genres'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = DB::transaction(function () use ($request) {
            $book = Book::create([
                'user_id' => $request->user()->id,
                'title' => $request->validated('title'),
                'author' => $request->validated('author'),
                'isbn' => $request->validated('isbn'),
                'published_at' => $request->validated('published_at'),
                'description' => $request->validated('description'),
                'image_url' => $request->validated('image_url'),
            ]);

            $book->genres()->sync($request->validated('genre_ids'));

            return $book;
        });

        return redirect()
            ->route('books.show', $book)
            ->with('status', '書籍を登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load([
            'genres',
            'user',
            'reviews' => fn($query) => $query->with('user')->latest(),
        ]);

        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $genres = Genre::orderBy('name')->get();
        $selectedGenreIds = $book->genres()->pluck('genres.id')->toArray();

        return view('books.edit', compact('book', 'genres', 'selectedGenreIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);

        DB::transaction(function () use ($request, $book) {
            $book->update([
                'title' => $request->validated('title'),
                'author' => $request->validated('author'),
                'isbn' => $request->validated('isbn'),
                'published_at' => $request->validated('published_at'),
                'description' => $request->validated('description'),
                'image_url' => $request->validated('image_url'),
            ]);

            $book->genres()->sync($request->validated('genre_ids'));
        });

        return redirect()
            ->route('books.show', $book)
            ->with('status', '書籍を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('status', '書籍を削除しました。');
    }
}
