<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\book;

class BookController extends Controller
{
    //
    public function index(Request $request)
    {
        return view('books');
    }
}
