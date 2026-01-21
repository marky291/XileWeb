<?php

namespace App\Services;

use Generator;
use RuntimeException;

class GpfParser
{
    private const HEADER = "Master of Magic\0";

    private const HEADER_LENGTH = 16;

    /**
     * Zlib headers vary by compression level:
     * - 0x78 0x01 = no compression
     * - 0x78 0x5E = fast compression
     * - 0x78 0x9C = default compression
     * - 0x78 0xDA = best compression
     */
    private const ZLIB_MARKERS = ["\x78\x01", "\x78\x5E", "\x78\x9C", "\x78\xDA"];

    /**
     * Extract and decompress all files from a GPF archive.
     *
     * GPF files contain multiple zlib-compressed files. This method
     * yields each decompressed file content.
     *
     * @param  string  $filePath  Path to the GPF file
     * @return Generator<string> Yields decompressed file contents
     *
     * @throws RuntimeException If the file is invalid
     */
    public function extractAll(string $filePath): Generator
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("GPF file not found: {$filePath}");
        }

        $data = file_get_contents($filePath);

        if ($data === false) {
            throw new RuntimeException("Failed to read GPF file: {$filePath}");
        }

        yield from $this->extractAllFromString($data);
    }

    /**
     * Extract and decompress all files from GPF data.
     *
     * @param  string  $data  Raw GPF file content
     * @return Generator<string> Yields decompressed file contents
     *
     * @throws RuntimeException If the data is invalid
     */
    public function extractAllFromString(string $data): Generator
    {
        $this->validateHeader($data);

        $positions = $this->findZlibPositions($data);

        foreach ($positions as $pos) {
            $compressed = substr($data, $pos);
            $decompressed = @gzuncompress($compressed);

            if ($decompressed !== false && strlen($decompressed) > 0) {
                yield $decompressed;
            }
        }
    }

    /**
     * Find ItemInfo content within a GPF file.
     *
     * Searches through all compressed files in the GPF archive
     * to find the one containing ItemInfo data.
     *
     * @param  string  $filePath  Path to the GPF file
     * @return string|null ItemInfo content or null if not found
     *
     * @throws RuntimeException If the file is invalid
     */
    public function findItemInfo(string $filePath): ?string
    {
        foreach ($this->extractAll($filePath) as $content) {
            if ($this->hasItemInfo($content)) {
                return $content;
            }
        }

        return null;
    }

    /**
     * Find ItemInfo content within GPF data.
     *
     * @param  string  $data  Raw GPF file content
     * @return string|null ItemInfo content or null if not found
     */
    public function findItemInfoFromString(string $data): ?string
    {
        foreach ($this->extractAllFromString($data) as $content) {
            if ($this->hasItemInfo($content)) {
                return $content;
            }
        }

        return null;
    }

    /**
     * Check if the content contains ItemInfo data.
     *
     * @param  string  $content  Decompressed GPF content
     * @return bool True if ItemInfo data is present
     */
    public function hasItemInfo(string $content): bool
    {
        return str_contains($content, 'tbl = {') && str_contains($content, 'identifiedDisplayName');
    }

    /**
     * Validate the GPF file header.
     *
     * @throws RuntimeException If the header is invalid
     */
    private function validateHeader(string $data): void
    {
        if (strlen($data) < self::HEADER_LENGTH) {
            throw new RuntimeException('GPF file is too small to be valid');
        }

        $header = substr($data, 0, self::HEADER_LENGTH);
        if ($header !== self::HEADER) {
            throw new RuntimeException('Invalid GPF file header');
        }
    }

    /**
     * Find all zlib stream positions in the data.
     *
     * @return array<int> Sorted array of positions
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

        return array_unique($positions);
    }
}
