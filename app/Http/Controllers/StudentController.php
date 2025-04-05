<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Welcome to the Admin Dashboard']);
    }
}
