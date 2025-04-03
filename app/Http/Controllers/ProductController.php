<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $exchangeRate = $this->getExchangeRate();

        return view('products.list', compact('products', 'exchangeRate'));
    }

    public function show(Request $request, $product_id)
    {
        $product = Product::find($product_id);
        $exchangeRate = $this->getExchangeRate();

        return view('products.show', compact('product', 'exchangeRate'));
    }

    /**
     * @return float
     */
    private function getExchangeRate()
    {
        return Cache::remember('exchange_rate_eur', 60 * 30, function () {
            try {
                $response = Http::timeout(5)->get("https://open.er-api.com/v6/latest/USD");

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['rates']['EUR'] ?? env('EXCHANGE_RATE', 0.85);
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch exchange rate: ' . $e->getMessage());
            }

            return env('EXCHANGE_RATE', 0.85);
        });
    }
}
