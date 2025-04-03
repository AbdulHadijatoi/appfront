<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendPriceChangeNotification;
use App\Models\Product;
use App\Traits\EmailNotification;
use App\Traits\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ImageOptimizer, EmailNotification;
    
    public function products()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    public function addProductForm()
    {
        return view('admin.products.add_product');
    }

    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price
        ]);

        if ($request->hasFile('image')) {
            // Use the optimizeImage function from the trait
            $product->image = $this->optimizeImage($request->file('image'), 'uploads');
        } else {
            $product->image = 'uploads/product-placeholder.jpg';
        }

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product added successfully');
    }

    public function editProduct($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return redirect()->route('admin.products')->with('error', 'Product not found.');
        }
        return view('admin.products.edit_product', compact('product'));
    }

    public function updateProduct(Request $request, $id)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product = Product::find($id);
        if (!$product) {
            return redirect()->route('admin.products')->with('error', 'Product not found.');
        }

        // Store the old price before updating
        $oldPrice = $product->price;

        // Update product details
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }

            // Use the optimizeImage function from the trait
            $product->image = $this->optimizeImage($request->file('image'), 'uploads');
        }

        $product->save();

        $this->sendPriceChangeEmailNotification($oldPrice, $product);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully');
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if(!$product) {
            return redirect()->route('admin.products')->with('error', 'Product not found.');
        }

        if ($product->image && Storage::exists($product->image)) {
            Storage::delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

}
