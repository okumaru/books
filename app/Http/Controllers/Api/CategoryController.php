<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = 10;
        $onpage = $_GET['page'];
        $doskip = ($onpage - 1) * $limit;

        $cat = DB::table('categories');

        if (isset($_GET['name']))
            $cat->where('name', $_GET['name']);

        if (isset($_GET['desc']))
            $cat->where('desc', $_GET['desc']);

        $datacats = $cat->skip($doskip)->take($limit)->get()->toArray();
        return response()->json($datacats);
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
        $category = category::create([
            'name' => $data['name'],
            'desc' => $data['desc'],
            'parent' => $data['parent']
        ]);

        return response()->json($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = category::where('id', $id)->first();
        return response()->json($category);
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
        category::where('id', $id)->update($data);

        return response()->json('success update category with id: ' . $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        category::where('id', $id)->delete();
        return response()->json('success delete category with id: ' . $id);
    }
}
