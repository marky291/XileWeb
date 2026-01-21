<?php

namespace Tests\Unit\Actions;

use App\Actions\CreateEmblemFromData;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateEmblemFromDataTest extends TestCase
{
    #[Test]
    public function it_returns_false_for_wrong_magic_number(): void
    {
        // Create data with wrong magic number (not 0x4D42 / "BM")
        // Need enough data for unpack to work
        $data = pack('v', 0x1234) . str_repeat("\x00", 52);

        $result = CreateEmblemFromData::run($data);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_accepts_valid_24bit_bmp(): void
    {
        // Create a minimal valid 24-bit BMP (2x2 pixel to ensure proper alignment)
        $bmp = $this->create24BitBmp(2, 2, [
            [[255, 0, 0], [0, 255, 0]],
            [[0, 0, 255], [255, 255, 0]],
        ]);

        $result = CreateEmblemFromData::run($bmp);

        $this->assertNotFalse($result);
        $this->assertTrue(is_resource($result) || $result instanceof \GdImage);

        // Verify dimensions
        $this->assertEquals(2, imagesx($result));
        $this->assertEquals(2, imagesy($result));

        imagedestroy($result);
    }

    #[Test]
    public function it_handles_larger_24bit_bmp(): void
    {
        // Create a 4x4 pixel BMP
        $pixels = [];
        for ($y = 0; $y < 4; $y++) {
            $pixels[$y] = [];
            for ($x = 0; $x < 4; $x++) {
                $pixels[$y][$x] = [128, 128, 128];
            }
        }
        $bmp = $this->create24BitBmp(4, 4, $pixels);

        $result = CreateEmblemFromData::run($bmp);

        $this->assertNotFalse($result);
        $this->assertEquals(4, imagesx($result));
        $this->assertEquals(4, imagesy($result));

        imagedestroy($result);
    }

    #[Test]
    public function it_handles_8bit_bmp(): void
    {
        $bmp = $this->create8BitBmp(2, 2);

        $result = CreateEmblemFromData::run($bmp);

        $this->assertNotFalse($result);
        $this->assertEquals(2, imagesx($result));
        $this->assertEquals(2, imagesy($result));

        imagedestroy($result);
    }

    #[Test]
    public function it_returns_gd_resource_or_image(): void
    {
        $bmp = $this->create24BitBmp(2, 2, [
            [[0, 0, 0], [0, 0, 0]],
            [[0, 0, 0], [0, 0, 0]],
        ]);

        $result = CreateEmblemFromData::run($bmp);

        // In PHP 8+, GD resources are GdImage objects
        $this->assertTrue(is_resource($result) || $result instanceof \GdImage);

        if (is_resource($result) || $result instanceof \GdImage) {
            imagedestroy($result);
        }
    }

    #[Test]
    public function it_creates_true_color_image(): void
    {
        $bmp = $this->create24BitBmp(2, 2, [
            [[255, 0, 0], [0, 255, 0]],
            [[0, 0, 255], [128, 128, 128]],
        ]);

        $result = CreateEmblemFromData::run($bmp);

        $this->assertNotFalse($result);
        // imageistruecolor returns true for truecolor images
        $this->assertTrue(imageistruecolor($result));

        imagedestroy($result);
    }

    /**
     * Create a minimal 24-bit BMP
     *
     * @param  int  $width
     * @param  int  $height
     * @param  array  $pixels  Array of [r, g, b] values, indexed [row][col]
     * @return string
     */
    private function create24BitBmp(int $width, int $height, array $pixels): string
    {
        $rowSize = ($width * 3 + 3) & ~3; // Row size must be multiple of 4
        $padding = $rowSize - ($width * 3);
        $imageSize = $rowSize * $height;
        $fileSize = 54 + $imageSize;

        // BMP Header (14 bytes)
        $header = pack('v', 0x4D42); // Magic number "BM"
        $header .= pack('V', $fileSize); // File size
        $header .= pack('v', 0); // Reserved
        $header .= pack('v', 0); // Reserved
        $header .= pack('V', 54); // Offset to pixel data

        // DIB Header (40 bytes) - BITMAPINFOHEADER
        $dib = pack('V', 40); // Header size
        $dib .= pack('V', $width); // Width
        $dib .= pack('V', $height); // Height
        $dib .= pack('v', 1); // Planes
        $dib .= pack('v', 24); // Bits per pixel
        $dib .= pack('V', 0); // Compression (none)
        $dib .= pack('V', $imageSize); // Image size
        $dib .= pack('V', 2835); // X pixels per meter
        $dib .= pack('V', 2835); // Y pixels per meter
        $dib .= pack('V', 0); // Colors in color table
        $dib .= pack('V', 0); // Important colors

        // Pixel data (bottom-up)
        $pixelData = '';
        for ($y = $height - 1; $y >= 0; $y--) {
            for ($x = 0; $x < $width; $x++) {
                $pixel = $pixels[$y][$x] ?? [0, 0, 0];
                // BMP stores as BGR
                $pixelData .= chr($pixel[2]).chr($pixel[1]).chr($pixel[0]);
            }
            // Add padding
            $pixelData .= str_repeat("\x00", $padding);
        }

        return $header.$dib.$pixelData;
    }

    /**
     * Create a minimal 8-bit BMP with a simple palette
     */
    private function create8BitBmp(int $width, int $height): string
    {
        $rowSize = ($width + 3) & ~3; // Row size must be multiple of 4
        $padding = $rowSize - $width;
        $imageSize = $rowSize * $height;
        $paletteSize = 256 * 4; // 256 colors, 4 bytes each (BGRA)
        $fileSize = 54 + $paletteSize + $imageSize;

        // BMP Header (14 bytes)
        $header = pack('v', 0x4D42); // Magic number "BM"
        $header .= pack('V', $fileSize); // File size
        $header .= pack('v', 0); // Reserved
        $header .= pack('v', 0); // Reserved
        $header .= pack('V', 54 + $paletteSize); // Offset to pixel data

        // DIB Header (40 bytes) - BITMAPINFOHEADER
        $dib = pack('V', 40); // Header size
        $dib .= pack('V', $width); // Width
        $dib .= pack('V', $height); // Height
        $dib .= pack('v', 1); // Planes
        $dib .= pack('v', 8); // Bits per pixel
        $dib .= pack('V', 0); // Compression (none)
        $dib .= pack('V', $imageSize); // Image size
        $dib .= pack('V', 2835); // X pixels per meter
        $dib .= pack('V', 2835); // Y pixels per meter
        $dib .= pack('V', 256); // Colors in color table
        $dib .= pack('V', 0); // Important colors

        // Color palette (256 colors)
        $palette = '';
        for ($i = 0; $i < 256; $i++) {
            // Simple grayscale palette
            $palette .= chr($i).chr($i).chr($i).chr(0); // BGRA
        }

        // Pixel data (bottom-up)
        $pixelData = '';
        for ($y = $height - 1; $y >= 0; $y--) {
            for ($x = 0; $x < $width; $x++) {
                $pixelData .= chr(128); // Gray color from palette
            }
            $pixelData .= str_repeat("\x00", $padding);
        }

        return $header.$dib.$palette.$pixelData;
    }
}
