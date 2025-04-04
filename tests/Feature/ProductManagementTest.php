<?php

namespace Tests\Feature;

use App\Jobs\SendPriceChangeNotification;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    #[Test]
    public function it_displays_the_product_listing_page()
    {
        $this->authenticateUser();

        Product::factory()->count(3)->create();

        $response = $this->get(route('admin.products'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
        $response->assertViewHas('products');
    }

    #[Test]
    public function it_displays_the_add_product_form()
    {
        $this->authenticateUser();
        $response = $this->get(route('admin.add.product'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.add_product');
    }

    #[Test]
    public function it_allows_a_logged_in_user_to_add_a_product()
    {
        $this->authenticateUser();
        
        $response = $this->post(route('admin.add.product.submit'), [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
        ]);

        $response->assertRedirect(route('admin.products'));
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    #[Test]
    public function it_validates_required_fields_when_adding_a_product()
    {
        $this->authenticateUser();

        $response = $this->post(route('admin.add.product.submit'), [
            'name' => '',
            'price' => -5,
        ]);

        $response->assertSessionHasErrors(['name', 'price']);
    }

    #[Test]
    public function it_allows_a_user_to_upload_an_image_when_adding_a_product()
    {
        $this->authenticateUser();
        Storage::fake('public');

        $image = UploadedFile::fake()->image('product.jpg');

        $response = $this->post(route('admin.add.product.submit'), [
            'name' => 'Product with Image',
            'description' => 'Test Description',
            'price' => 200,
            'image' => $image
        ]);

        $response->assertRedirect(route('admin.products'));

        $this->assertDatabaseHas('products', ['name' => 'Product with Image']);
        Storage::disk('public')->assertExists('uploads/' . $image->hashName());
    }

    #[Test]
    public function it_displays_the_edit_product_page()
    {
        $this->authenticateUser();
        $product = Product::factory()->create();

        $response = $this->get(route('admin.edit.product', $product->id));
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.edit_product');
        $response->assertViewHas('product');
    }

    #[Test]
    public function it_allows_a_logged_in_user_to_update_a_product()
    {
        $this->authenticateUser();
        $product = Product::factory()->create(['price' => 100]);

        $response = $this->put(route('admin.update.product', $product->id), [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 150,
        ]);

        $response->assertRedirect(route('admin.products'));
        $this->assertDatabaseHas('products', ['name' => 'Updated Product', 'price' => 150]);
    }

    #[Test]
    public function it_sends_email_when_price_changes()
    {
        Queue::fake(); // Fake queue for job assertion
        $this->authenticateUser();

        $product = Product::factory()->create(['price' => 100]);

        $response = $this->put(route('admin.update.product', $product->id), [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 200, // Price changed
        ]);

        $response->assertRedirect(route('admin.products'));

        // Assert that the job was dispatched correctly
        Queue::assertPushed(SendPriceChangeNotification::class, function ($job) use ($product) {
            $jobProduct = (new \ReflectionClass($job))->getProperty('product');
            $jobProduct->setAccessible(true);
            return $jobProduct->getValue($job)->id === $product->id;
        });

    }

    #[Test]
    public function it_allows_a_user_to_update_an_image()
    {
        $this->authenticateUser();
        Storage::fake('public');

        $product = Product::factory()->create();

        $newImage = UploadedFile::fake()->image('new-product.jpg');

        $response = $this->put(route('admin.update.product', $product->id), [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'price' => 120,
            'image' => $newImage
        ]);

        $response->assertRedirect(route('admin.products'));

        Storage::disk('public')->assertExists('uploads/' . $newImage->hashName());
    }

    #[Test]
    public function it_returns_error_if_product_does_not_exist_when_updating()
    {
        $this->authenticateUser();

        $response = $this->put(route('admin.update.product', 999), [
            'name' => 'Non-existing Product',
            'price' => 100
        ]);

        $response->assertRedirect(route('admin.products'));
        $response->assertSessionHas('error', 'Product not found.');
    }

    #[Test]
    public function it_allows_a_logged_in_user_to_delete_a_product()
    {
        $this->authenticateUser();
        $product = Product::factory()->create();

        $response = $this->delete(route('admin.delete.product', $product->id));

        $response->assertRedirect(route('admin.products'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    #[Test]
    public function it_returns_error_if_product_does_not_exist_when_deleting()
    {
        $this->authenticateUser();

        $response = $this->delete(route('admin.delete.product', 999));

        $response->assertRedirect(route('admin.products'));
        $response->assertSessionHas('error', 'Product not found.');
    }
}
