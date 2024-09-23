<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Models\Company;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index() 
    {
        $term = Term::first();

        return view('terms.index', compact('term'));
    }
}
