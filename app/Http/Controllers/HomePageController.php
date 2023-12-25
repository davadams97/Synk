<?php

namespace App\Http\Controllers;

use Inertia\Response;

class HomePageController extends Controller
{
    public function index(): Response
    {
        return inertia('Home/Index');
    }
}
