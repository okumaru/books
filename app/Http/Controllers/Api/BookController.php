<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Publisher;
use App\Models\Book;
use App\Models\Category;
use App\Models\Keyword;

class BookController extends Controller
{

    private String $title;

    private Int $price;

    private String $desc;

    private Int $stock;

    private String $publisher;

    private array $arrcats;

    private array $arrkeys;

    private array $mcats;

    private array $mkeys;

    private function setParams(array $params)
    {
        $this->title = $params['title'];
        $this->price = $params['price'];
        $this->desc = $params['desc'] ?? '';
        $this->stock = $params['stock'] ?? 0;
        $this->publisher = $params['publisher'];
        $this->arrcats = isset($params['categories']) ? explode(',', $params['categories']) : [];
        $this->arrkeys = isset($params['keywords']) ? explode(',', $params['keywords']) : [];
    }

    private function setMCats()
    {
        $dataCats = array_unique($this->arrcats);
        $this->mcats = array_map(function ($cat) {
            $catModel = new Category();
            $catModel->name = $cat;
            $catModel->description = '';
            $catModel->parent_id = 0;

            return $catModel;
        }, $dataCats);
    }

    private function setMKeys()
    {
        $dataKeys = array_unique($this->arrkeys);
        $this->mkeys = array_map(function ($key) {
            $keyModel = new Keyword();
            $keyModel->name = $key;

            return $keyModel;
        }, $dataKeys);
    }

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
        $reqdata = $request->json()->all();

        $this->setParams($reqdata);

        # Set data category model
        $this->setMCats();

        # Set data keys model
        $this->setMKeys();

        # get first or insert data publisher
        $datapublisher = Publisher::firstOrCreate(['name' => $this->publisher]);

        $book = new Book();
        $book->title = $this->title;
        $book->description = $this->desc;
        $book->price = $this->price;
        $book->stock = $this->stock;
        $book->publisher()->associate($datapublisher); # append data publisher to book
        $book->save();

        $book->categories()->saveMany($this->mcats); # add data categories with book_id from data book
        $book->keywords()->saveMany($this->mkeys); # add data keywords with book_id from data book

        return response()->json('Success add book.');
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
