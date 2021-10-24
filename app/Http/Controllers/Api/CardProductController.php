<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\CardProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class CardProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {

        $products = auth()->user()->card_products->load('specifications');
        $amount = $products->sum('pivot.total_price_quantity');
        $charges = Helpers::get_charges_products($amount);

        return response()->json([
            'products' => $products,
            'charges' => $charges
        ]);
        
    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$user=auth()->user();
        $user = auth()->user();
        $products = $user->card_products->load('specifications');

        $product = $products->firstWhere('id', $request->product_id);

        //cambiar cantidad
        if ($product &&  $product->availables >= $request->quantity) {
            $product->pivot->quantity = $request->quantity;
            $product->pivot->total_price_quantity = $product->price * $request->quantity;
            $product->pivot->save();
        }

        //agregar nuevo producto al carrito
        if (!$product  && $request->purchase == false) {

            $product = Product::where('id', $request->product_id)->where('availables', '>=', $request->quantity)->first();
            if ($product) {
                $user->card_products()->attach($product->id, [
                    'quantity' => $request->quantity,
                    'total_price_quantity' => $product->price * $request->quantity
                ]);
                $products = $user->card_products()->with('specifications')->get();
            }
        }


        $amount = $products->sum('pivot.total_price_quantity');
        $charges = Helpers::get_charges_products($amount);


        return response()->json([
            'products' => $products,
            'charges' => $charges
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $user->card_products()->detach($id);
        $products = $user->card_products()->with('specifications')->get();
        $amount = $products->sum('pivot.total_price_quantity');
        $charges = Helpers::get_charges_products($amount);
        return response()->json([
            'products' => $products,
            'charges' => $charges
        ]);
    }
}
