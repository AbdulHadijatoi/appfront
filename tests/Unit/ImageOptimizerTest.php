<?php

namespace Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Traits\ImageOptimizer;
use PHPUnit\Framework\Attributes\Test;

class ImageOptimizerTest extends TestCase
{
    use ImageOptimizer;

    #[Test]
    public function it_optimizes_image_when_over_1mb()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('large_image.jpg')->size(2048); // 2MB

        $path = $this->optimizeImage($file, 'uploads');

        Storage::disk('public')->assertExists($path);
    }

    #[Test]
    public function it_stores_image_directly_when_under_1mb()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('small_image.jpg')->size(500); // 500KB

        $path = $this->optimizeImage($file, 'uploads');

        Storage::disk('public')->assertExists($path);
    }
}
