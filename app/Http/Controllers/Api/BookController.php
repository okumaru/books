<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

        $limit = 5;
        $reqdata = $request->json()->all();

        $book = Book::with([
            'categories',
            'keywords',
            'publisher'
        ]);

        if (isset($reqdata['title']))
            $book->where('title', $reqdata['title']);

        if (isset($reqdata['desc']))
            $book->where('desc', 'like', "%{$reqdata['desc']}%");

        if (isset($reqdata['price']))
            $book->where('price', $reqdata['price']);

        if (isset($reqdata['cat']))
            $book->whereRelation('categories', 'name', $reqdata['cat']);

        if (isset($reqdata['key']))
            $book->whereRelation('keywords', 'name', $reqdata['key']);

        if (isset($reqdata['publisher']))
            $book->whereRelation('publisher', 'name', $reqdata['publisher']);

        $databooks = $book->paginate($limit);

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
        $book = Book::with([
            'categories',
            'keywords',
            'publisher'
        ])->where('id', $id)->first();
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
        $reqdata = $request->json()->all();

        #
        # get first or insert data publisher
        $datapublisher = Publisher::firstOrCreate(['name' => $reqdata['publisher']]);

        $book = Book::find($id);
        $book->publisher()->associate($datapublisher); # append data publisher to book
        $book->update($reqdata);

        #
        # Manipulate data categories to add and delete
        $cats = isset($reqdata['categories']) ? explode(',', $reqdata['categories']) : [];
        $unicats = array_unique($cats);
        $bookcats = $book->categories()->get();

        # Delete categories if not in params categories
        $bookcats->each(function ($cat) use ($unicats) {
            if (!in_array($cat->name, $unicats)) $cat->delete();
        });

        # Add new cats if not exist
        $arrbookcats = array_column($bookcats->toArray(), 'name');
        $this->arrcats = array_filter($unicats, function ($cat) use ($arrbookcats) {
            return !in_array($cat, $arrbookcats);
        });
        $this->setMCats();
        $book->categories()->saveMany($this->mcats);
        # 
        # Finish!
        # 

        # Manipulate data keywords to add and delete
        # Start!
        $keys = isset($reqdata['keywords']) ? explode(',', $reqdata['keywords']) : [];
        $unikeys = array_unique($keys);
        $bookkeys = $book->keywords()->get();

        # Delete keywords if not in params keywords
        $bookkeys->each(function ($key) use ($unikeys) {
            if (!in_array($key->name, $unikeys)) $key->delete();
        });

        # Add new keywords if not exist
        $arrbookkeys = array_column($bookkeys->toArray(), 'name');
        $this->arrkeys = array_filter($unikeys, function ($key) use ($arrbookkeys) {
            return !in_array($key, $arrbookkeys);
        });
        $this->setMKeys();
        $book->keywords()->saveMany($this->mkeys);
        #
        # Finish!
        #

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
        $book = Book::find($id);
        $book->categories()->delete();
        $book->keywords()->delete();
        $book->delete();

        return response()->json('Success delete book.');
    }
}
