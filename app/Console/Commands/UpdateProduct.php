<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Jobs\SendPriceChangeNotification;
use App\Traits\EmailNotification;
use Illuminate\Support\Facades\Log;

class UpdateProduct extends Command
{
    use EmailNotification;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update {id} {--name=} {--description=} {--price=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a product with the specified details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $product = Product::find($id);

        if (!$product) {
            $this->error("Product with ID {$id} not found.");
            return 1;
        }

        $data = [];

        // Validate and update name
        if ($this->option('name')) {
            $name = trim($this->option('name'));
            if (strlen($name) < 3) {
                $this->error("Name must be at least 3 characters long.");
                return 1;
            }
            $data['name'] = $name;
        }

        // Update description if provided
        if ($this->option('description')) {
            $data['description'] = $this->option('description');
        }

        // Validate and update price
        if ($this->option('price') !== null) {
            if (!is_numeric($this->option('price')) || $this->option('price') < 0) {
                $this->error("Price must be a valid positive number.");
                return 1;
            }
            $data['price'] = $this->option('price');
        }

        // Check if any changes were provided
        if (empty($data)) {
            $this->info("No changes provided. Product remains unchanged.");
            return 0;
        }

        $oldPrice = $product->price;
        $product->update($data);
        $this->info("Product updated successfully.");

        // Send price change notification if price changed
        if (isset($data['price']) && $oldPrice != $product->price) {
            $this->info("Price changed from {$oldPrice} to {$product->price}.");

            $this->sendPriceChangeEmailNotification($oldPrice, $product);
        }

        return 0;
    }
}
