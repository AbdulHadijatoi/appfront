<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductDisplayTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_product_list()
    {
        // Arrange: Create some products
        Product::factory()->count(3)->create();

        // Act: Visit the product list page
        $response = $this->get(route('home'));

        // Assert: Ensure the page loads and displays products
        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    #[Test]
    public function it_displays_product_details_page()
    {
        // Arrange: Create a test product
        $product = Product::factory()->create();

        // Act: Visit the product details page
        $response = $this->get(route('products.show', $product->id));

        // Assert: Ensure the page loads and shows correct product
        $response->assertStatus(200);
        $response->assertViewHas('product', $product);
    }

    #[Test]
    public function it_fetches_exchange_rate_successfully()
    {
        // Arrange: Fake a successful API response
        Http::fake([
            'https://open.er-api.com/v6/latest/USD' => Http::response([
                'rates' => ['EUR' => 0.90],
            ], 200),
        ]);

        // Act: Visit the product page (which calls getExchangeRate)
        $this->get(route('home'));

        // Assert: Ensure the exchange rate is stored in cache
        $this->assertEquals(0.90, Cache::get('exchange_rate_eur'));
    }

    #[Test]
    public function it_uses_fallback_exchange_rate_on_api_failure()
    {
        // Arrange: Fake a failed API response
        Http::fake([
            'https://open.er-api.com/v6/latest/USD' => Http::response([], 500),
        ]);

        // Act: Visit the product page
        $this->get(route('home'));

        // Assert: Ensure fallback rate is used
        $this->assertEquals(env('EXCHANGE_RATE', 0.85), Cache::get('exchange_rate_eur'));
    }

}
