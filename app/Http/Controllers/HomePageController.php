<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;

class HomePageController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Home/Index');
    }
}
