<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers', ['customers' => Customer::orderBy('id')->get()]);
    }
}
