<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Overstocks;
use App\Models\Supplie;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    //views
    public function index(){
        $product = Product::with('supplier')->get();
        $supplier = DB::table('supplies')->get();
        if(!$product){
            return response([
                'error' => 'notFound'
            ]);
        }
        return view('liquor-data.show', [
            'product' => $product,
            'supplier' => $supplier
        ]);
    }

    public function showAdminDashboard()
    {
        return view('dashboard.admin');
    }

    public function returnCreateDataView(){
        //$supplier = DB::table('supplies')->pluck('name')->get();
        return view('liquor-data.create', compact('supplier'));
    }

    // public function showCreateViewInDashboard()
    // {
    //     return view('dashboard.create');
    // }

    public function getPrice(Request $request, $selectedValue)
    {
        $price = DB::table('products')->where('name', $selectedValue)->value('unit_price');
        return response()->json(['unit_price' => $price]);
    }
    //post Liquor Data

    public function storeData(Request $request){
        $number = mt_rand(1000000000,9999999999);

        $request['product_code'] = $number;
        if($this->productCodeExists($number)){
            $number = mt_rand(1000000000,9999999999);
        }

        $unit_price = $request['unit_price'];
        $quantity = $request['quantity'];

        $request['inventory_value'] = $unit_price * $quantity; 

        $product=Product::create($request->all());

        $suppliers = Supplie::findOrFail($request['supplier_id']);
        $product->supplier()->associate($suppliers);
        $product->save();

        return redirect('/dashboard');
    }


    //edit
    public function editData(Request $request,$id){
        $product = Product::findOrFail($id);
        $unit_price = $request['unit_price'];
        $quantity = $request['quantity'];
        $request['inventory_value'] = $unit_price * $quantity; 
        $product->update($request->all());
    }




    public function productCodeExists($number){
        return Product::whereProductCode($number)->exists();
    }

    public function checkOverstock(){
        $stocks = Overstocks::all();
        return response([
            'stocks' => $stocks
        ]);
    }


    //For mobile
    public function showStocksMobile(){
        $product = Product::all();

        return response([
            'products' => $product
        ]);
    }

    public function storeStock(Request $request){
        $number = mt_rand(1000000000,9999999999);

        $request['product_code'] = $number;
        if($this->productCodeExists($number)){
            $number = mt_rand(1000000000,9999999999);
        }

        $unit_price = $request['unit_price'];
        $quantity = $request['quantity'];

        $request['inventory_value'] = $unit_price * $quantity; 

        $stock = Product::create($request->all());
        return response([
            'stock' => $stock
        ]);
    }


    public function edit(Request $request, $id)
    {
        $product = Product::find($id);
        $product->name = $request->input('name');
        $product->unit_price = $request->input('unit_price');
        $product->quantity = $request->input('quantity');
        $product->description = $request->input('description');
        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');

        $validatedData = $request->validate([
        'name' => 'required|max:255',
        'unit_price' => 'required|numeric',
        'quantity' => 'required|integer',
        'description' => 'nullable|max:255',
    ]);

    $product->update($validatedData);

    }

    public function getSupplier($id){
        $supplier = Supplie::with('products')->findOrFail($id);

        return response([
            'supplier' => $supplier,
            'instance' => $supplier->products
        ]); 
    }
    public function getallindex(){
        $product = Product::with('supplier')->get();
        $supplier = DB::table('supplies')->get();
        return response([
            'prod' => $product,
            'supp' => $supplier
        ]);
    }
    public function getsupp(){
        $product = Supplie::with('products')->get();
        //$supplier = DB::table('supplies')->get();
        return response([
            'prod' => $product,
            //'supp' => $supplier
        ]);
    }

}


