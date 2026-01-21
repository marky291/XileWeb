<?php

namespace App\Services;

use Generator;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class GrfImageExtractor
{
    private const HEADER = "Master of Magic\0";

    private const HEADER_LENGTH = 16;

    private const ZLIB_MARKERS = ["\x78\x01", "\x78\x5E", "\x78\x9C", "\x78\xDA"];

    /**
     * Extract file entries from a GPF/GRF archive.
     *
     * @param  string  $filePath  Path to the GPF file
     * @return Generator<array{path: string, filename: string, resource_name: string, offset: int, size: int}>
     */
    public function getFileEntries(string $filePath): Generator
    {
        $data = $this->readFile($filePath);
        $fileTable = $this->extractFileTable($data);

        if ($fileTable === null) {
            return;
        }

        yield from $this->parseFileTable($fileTable);
    }

    /**
     * Extract images from GPF and save them mapped to item IDs.
     *
     * @param  string  $filePath  Path to the GPF file
     * @param  string  $disk  Storage disk to save to
     * @param  array<string, int>  $resourceMap  Map of resource_name => item_id
     * @param  string  $pathPrefix  Prefix for output paths (e.g., 'xilero' or 'retro')
     * @return array{extracted: int, skipped: int, errors: array<string>, extracted_item_ids: array<int>}
     */
    public function extractImages(string $filePath, string $disk, array $resourceMap, string $pathPrefix = ''): array
    {
        $data = $this->readFile($filePath);
        $fileTable = $this->extractFileTable($data);

        if ($fileTable === null) {
            return [
                'extracted' => 0,
                'skipped' => 0,
                'errors' => ['Could not find file table in GPF'],
                'extracted_item_ids' => [],
            ];
        }

        $extracted = 0;
        $skipped = 0;
        $errors = [];
        $extractedItemIds = [];

        // Extract all valid BMP images from zlib streams
        $bmpImages = $this->extractAllBmpImages($data);

        // Parse file entries and match with extracted images by order
        $entries = iterator_to_array($this->parseFileTable($fileTable));
        $imageIndex = 0;

        foreach ($entries as $entry) {
            $resourceName = $entry['resource_name'];
            $pathLower = strtolower($entry['path']);

            // Only extract images from /collection or /item folders
            $isCollectionPath = str_contains($pathLower, '\\collection\\') || str_contains($pathLower, '/collection/');
            $isItemPath = str_contains($pathLower, '\\item\\') || str_contains($pathLower, '/item/');

            if (! $isCollectionPath && ! $isItemPath) {
                $skipped++;
                $imageIndex++;

                continue;
            }

            // Check if we have this resource in our map
            if (! isset($resourceMap[$resourceName])) {
                $skipped++;
                $imageIndex++;

                continue;
            }

            $itemId = $resourceMap[$resourceName];

            if (! isset($bmpImages[$imageIndex])) {
                $errors[] = "No image at index {$imageIndex} for: {$resourceName}";
                $imageIndex++;

                continue;
            }

            try {
                $subfolder = $isCollectionPath ? 'collection' : 'item';
                $outputPath = $this->buildOutputPath($itemId, $pathPrefix, $subfolder);
                $pngData = $this->convertBmpToPng($bmpImages[$imageIndex]);

                if ($pngData === null) {
                    $errors[] = "Could not convert BMP to PNG for: {$resourceName}";
                    $imageIndex++;

                    continue;
                }

                Storage::disk($disk)->put($outputPath, $pngData);
                $extracted++;
                $extractedItemIds[] = $itemId;
            } catch (\Throwable $e) {
                $errors[] = "Error processing {$resourceName}: {$e->getMessage()}";
            }

            $imageIndex++;
        }

        return [
            'extracted' => $extracted,
            'skipped' => $skipped,
            'errors' => $errors,
            'extracted_item_ids' => array_unique($extractedItemIds),
        ];
    }

    /**
     * Extract all BMP images from zlib streams in order.
     *
     * @return array<int, string>
     */
    private function extractAllBmpImages(string $data): array
    {
        $images = [];
        $positions = $this->findZlibPositions($data);

        foreach ($positions as $pos) {
            $compressed = substr($data, $pos);
            $decompressed = @gzuncompress($compressed);

            // Only keep valid BMP images
            if ($decompressed !== false && substr($decompressed, 0, 2) === 'BM') {
                $images[] = $decompressed;
            }
        }

        return $images;
    }

    /**
     * Read and validate GPF file.
     */
    private function readFile(string $filePath): string
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("GPF file not found: {$filePath}");
        }

        $data = file_get_contents($filePath);

        if ($data === false) {
            throw new RuntimeException("Failed to read GPF file: {$filePath}");
        }

        if (strlen($data) < self::HEADER_LENGTH) {
            throw new RuntimeException('GPF file is too small');
        }

        if (substr($data, 0, self::HEADER_LENGTH) !== self::HEADER) {
            throw new RuntimeException('Invalid GPF file header');
        }

        return $data;
    }

    /**
     * Extract the file table from the end of the GPF.
     */
    private function extractFileTable(string $data): ?string
    {
        // File table is typically in the last 100KB, compressed with zlib
        $searchSize = min(100000, strlen($data) - self::HEADER_LENGTH);
        $endArea = substr($data, -$searchSize);

        // Find all zlib streams and check each for file table content
        $positions = [];
        foreach (self::ZLIB_MARKERS as $marker) {
            $offset = 0;
            while (($pos = strpos($endArea, $marker, $offset)) !== false) {
                $positions[] = $pos;
                $offset = $pos + 1;
            }
        }

        // Sort positions and try each one
        sort($positions);
        $positions = array_unique($positions);

        foreach ($positions as $pos) {
            $compressed = substr($endArea, $pos);
            $decompressed = @gzuncompress($compressed);
            if ($decompressed !== false && strlen($decompressed) > 1000) {
                // Verify it looks like a file table (contains paths)
                if (str_contains($decompressed, 'data\\')) {
                    return $decompressed;
                }
            }
        }

        return null;
    }

    /**
     * Parse file table entries.
     *
     * @return Generator<array{path: string, filename: string, resource_name: string, index: int}>
     */
    private function parseFileTable(string $fileTable): Generator
    {
        $pos = 0;
        $index = 0;
        $len = strlen($fileTable);

        while ($pos < $len - 20) {
            // Find null terminator (end of path)
            $nullPos = strpos($fileTable, "\x00", $pos);
            if ($nullPos === false) {
                break;
            }

            $path = substr($fileTable, $pos, $nullPos - $pos);
            if (strlen($path) < 5) {
                break;
            }

            // Convert from CP949 to UTF-8
            $pathUtf8 = @mb_convert_encoding($path, 'UTF-8', 'CP949');

            // Extract filename
            $lastSlash = strrpos($pathUtf8, '\\');
            $filename = $lastSlash !== false ? substr($pathUtf8, $lastSlash + 1) : $pathUtf8;

            // Get resource name (filename without extension)
            $resourceName = pathinfo($filename, PATHINFO_FILENAME);

            // Only yield image files
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, ['bmp', 'png', 'tga'])) {
                yield [
                    'path' => $pathUtf8,
                    'filename' => $filename,
                    'resource_name' => $resourceName,
                    'index' => $index,
                ];
            }

            // Move to next entry (null byte + 17 bytes metadata)
            $pos = $nullPos + 18;
            $index++;
        }
    }

    /**
     * Find all zlib stream positions in the data.
     *
     * @return array<int>
     */
    private function findZlibPositions(string $data): array
    {
        $positions = [];

        foreach (self::ZLIB_MARKERS as $marker) {
            $offset = 0;
            while (($pos = strpos($data, $marker, $offset)) !== false) {
                $positions[] = $pos;
                $offset = $pos + 1;
            }
        }

        sort($positions);

        return array_values(array_unique($positions));
    }

    /**
     * Build the output path for an extracted image.
     */
    private function buildOutputPath(int $itemId, string $prefix, string $subfolder): string
    {
        $basePath = $prefix ? "{$prefix}/{$subfolder}" : $subfolder;

        return "{$basePath}/{$itemId}.png";
    }

    /**
     * Convert BMP image data to PNG with transparency.
     */
    private function convertBmpToPng(string $bmpData): ?string
    {
        // Check if it's actually a BMP
        if (substr($bmpData, 0, 2) !== 'BM') {
            // Might already be PNG or other format
            if (substr($bmpData, 0, 8) === "\x89PNG\r\n\x1a\n") {
                return $bmpData; // Already PNG
            }

            return null;
        }

        // Create image from BMP data
        $source = @imagecreatefromstring($bmpData);
        if ($source === false) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        // Create a true color image with alpha channel
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Fill with transparent
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);

        // Check if source is a paletted image
        $isPaletted = ! imageistruecolor($source);

        // Copy pixels, converting magenta to transparent
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $colorIndex = imagecolorat($source, $x, $y);

                // For paletted images, get RGB from the palette
                if ($isPaletted) {
                    $rgb = imagecolorsforindex($source, $colorIndex);
                    $r = $rgb['red'];
                    $g = $rgb['green'];
                    $b = $rgb['blue'];
                } else {
                    $r = ($colorIndex >> 16) & 0xFF;
                    $g = ($colorIndex >> 8) & 0xFF;
                    $b = $colorIndex & 0xFF;
                }

                // Magenta (255, 0, 255) is transparent in RO
                if ($r === 255 && $g === 0 && $b === 255) {
                    // Already transparent from fill
                    continue;
                }

                $newColor = imagecolorallocatealpha($image, $r, $g, $b, 0);
                imagesetpixel($image, $x, $y, $newColor);
            }
        }

        imagedestroy($source);

        // Output as PNG
        ob_start();
        imagepng($image, null, 9);
        $pngData = ob_get_clean();
        imagedestroy($image);

        return $pngData !== false ? $pngData : null;
    }
}
