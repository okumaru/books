<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\book;
use App\Models\bookcategory;
use App\Models\category;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $limit = 10;
        $onpage = $_GET['page'];
        $doskip = ($onpage - 1) * $limit;

        $book = DB::table('books');

        if (isset($_GET['title']))
            $book->where('title', $_GET['title']);

        if (isset($_GET['desc']))
            $book->where('desc', 'like', "%{$_GET['desc']}%");

        if (isset($_GET['keyword']))
            $book->where('keywords', 'like', "%{$_GET['keywords']}%");

        if (isset($_GET['price']))
            $book->where('price', $_GET['price']);

        if (isset($_GET['publisher']))
            $book->where('publisher', $_GET['publisher']);

        $databooks = $book->skip($doskip)->take($limit)->get()->toArray();

        array_map(function ($book) {
            $book->cats = DB::table('bookcategories')
                ->join('categories', 'categories.id', '=', 'bookcategories.category_id')
                ->where('bookcategories.book_id', $book->id)
                ->get()->toArray();
        }, $databooks);

        if (isset($_GET['cat'])) {
            $cat = $_GET['cat'];
            $databooks = array_filter($databooks, function ($book) use ($cat) {
                if (empty($book->cats)) return false;
                $arrcats = json_decode(json_encode($book->cats), true);
                $catnames = array_column($arrcats, 'name');

                return in_array($cat, $catnames);
            });
        }

        return response()->json($databooks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $book = book::create([
            'title' => $data['title'],
            'desc' => $data['desc'],
            'keywords' => $data['keywords'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'publisher' => $data['publisher'],
        ]);

        foreach ($data['cat'] as $cat) {
            bookcategory::create([
                'book_id' => $book->id,
                'category_id' => $cat,
            ]);
        }

        return response()->json($book);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = book::where('id', $id)->first();
        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);
        book::where('id', $id)->update($data);

        return response()->json('success update book with id: ' . $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        book::where('id', $id)->delete();
        return response()->json('success delete book with id: ' . $id);
    }
}
