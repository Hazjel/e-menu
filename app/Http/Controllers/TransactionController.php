<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function cart(Request $request) {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        return view('page.cart', compact('store'));
    }
}
