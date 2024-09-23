<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index() 
    {
        $company = Company::first();

        return view('company.index', compact('company'));
    }
}
