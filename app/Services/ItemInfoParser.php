<?php

namespace App\Services;

use Generator;

class ItemInfoParser
{
    /**
     * Parse ItemInfo Lua content and yield item entries.
     *
     * @param  string  $content  The Lua content starting with "tbl = {"
     * @return Generator<int, array{item_id: int, name: string, description: string|null, resource_name: string|null, view_id: int, slot_count: int}>
     */
    public function parse(string $content): Generator
    {
        // Find all item ID markers: [itemId] =
        $pattern = '/\[(\d+)\]\s*=\s*\{/';
        $offset = 0;

        while (preg_match($pattern, $content, $match, PREG_OFFSET_CAPTURE, $offset)) {
            $itemId = (int) $match[1][0];
            $braceStart = $match[0][1] + strlen($match[0][0]) - 1;

            // Find the matching closing brace
            $blockContent = $this->extractBalancedBraces($content, $braceStart);
            if ($blockContent !== null) {
                $item = $this->parseItemBlock($itemId, $blockContent);
                if ($item !== null) {
                    yield $item;
                }
            }

            // Move offset past this item
            $offset = $braceStart + strlen($blockContent ?? '') + 2;
        }
    }

    /**
     * Extract content within balanced braces.
     *
     * @param  string  $content  The full content
     * @param  int  $startPos  Position of opening brace
     * @return string|null Content within braces (excluding braces) or null
     */
    private function extractBalancedBraces(string $content, int $startPos): ?string
    {
        if ($content[$startPos] !== '{') {
            return null;
        }

        $depth = 1;
        $pos = $startPos + 1;
        $len = strlen($content);

        while ($depth > 0 && $pos < $len) {
            $char = $content[$pos];
            if ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;
            } elseif ($char === '"' || $char === "'") {
                // Skip quoted strings to avoid false brace matches
                $quote = $char;
                $pos++;
                while ($pos < $len && $content[$pos] !== $quote) {
                    if ($content[$pos] === '\\') {
                        $pos++; // Skip escaped char
                    }
                    $pos++;
                }
            }
            $pos++;
        }

        if ($depth !== 0) {
            return null;
        }

        return substr($content, $startPos + 1, $pos - $startPos - 2);
    }

    /**
     * Parse a single item block.
     *
     * @param  int  $itemId  The item ID
     * @param  string  $block  The item block content (without outer braces)
     * @return array{item_id: int, name: string, description: string|null, resource_name: string|null, view_id: int, slot_count: int}|null
     */
    private function parseItemBlock(int $itemId, string $block): ?array
    {
        $name = $this->extractString($block, 'identifiedDisplayName');
        if ($name === null) {
            return null;
        }

        $description = $this->extractDescription($block);
        $resourceName = $this->extractString($block, 'identifiedResourceName');
        $viewId = $this->extractInt($block, 'ClassNum');
        $slotCount = $this->extractInt($block, 'slotCount');

        return [
            'item_id' => $itemId,
            'name' => $name,
            'description' => $description,
            'resource_name' => $resourceName,
            'view_id' => $viewId,
            'slot_count' => $slotCount,
        ];
    }

    /**
     * Extract a string value from a Lua property.
     *
     * @param  string  $block  The item block content
     * @param  string  $property  The property name
     * @return string|null The extracted value or null
     */
    private function extractString(string $block, string $property): ?string
    {
        // Match: propertyName = "value" or propertyName = 'value'
        // Use word boundary to avoid matching substrings (e.g., "unidentifiedDisplayName" contains "identifiedDisplayName")
        // Use .*? with dotall to capture any characters including non-UTF8 bytes
        $pattern = '/(?<![a-zA-Z])'.preg_quote($property, '/').'\s*=\s*"(.*?)"|(?<![a-zA-Z])'.preg_quote($property, '/').'\s*=\s*\'(.*?)\'/s';

        if (preg_match($pattern, $block, $match)) {
            // Match is in group 1 (double quotes) or group 2 (single quotes)
            $value = $match[1] !== '' ? $match[1] : ($match[2] ?? '');

            return $this->cleanString($value);
        }

        return null;
    }

    /**
     * Extract the description from identifiedDescriptionName array.
     *
     * @param  string  $block  The item block content
     * @return string|null The concatenated description or null
     */
    private function extractDescription(string $block): ?string
    {
        // Find the position of identifiedDescriptionName (not unidentifiedDescriptionName)
        // Use regex to match word boundary
        if (! preg_match('/(?<![a-zA-Z])identifiedDescriptionName\s*=\s*\{/', $block, $match, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        $startPos = $match[0][1];

        // Find the opening brace
        $bracePos = strpos($block, '{', $startPos);
        if ($bracePos === false) {
            return null;
        }

        // Find matching closing brace (handle nested braces)
        $depth = 1;
        $pos = $bracePos + 1;
        $len = strlen($block);

        while ($depth > 0 && $pos < $len) {
            if ($block[$pos] === '{') {
                $depth++;
            } elseif ($block[$pos] === '}') {
                $depth--;
            }
            $pos++;
        }

        if ($depth !== 0) {
            return null;
        }

        $arrayContent = substr($block, $bracePos + 1, $pos - $bracePos - 2);

        // Extract all quoted strings from the array
        if (preg_match_all('/["\']([^"\']*)["\']/', $arrayContent, $strings)) {
            $lines = array_map([$this, 'cleanString'], $strings[1]);
            // Filter out placeholder entries like "..."
            $lines = array_filter($lines, fn ($line) => $line !== '...' && $line !== '');

            if (empty($lines)) {
                return null;
            }

            return implode("\n", $lines);
        }

        return null;
    }

    /**
     * Extract an integer value from a Lua property.
     *
     * @param  string  $block  The item block content
     * @param  string  $property  The property name
     * @return int The extracted value or 0
     */
    private function extractInt(string $block, string $property): int
    {
        // Match: propertyName = 123
        $pattern = '/'.preg_quote($property, '/').'\s*=\s*(\d+)/';

        if (preg_match($pattern, $block, $match)) {
            return (int) $match[1];
        }

        return 0;
    }

    /**
     * Clean a string value by removing escape sequences and normalizing.
     *
     * @param  string  $value  The raw string value
     * @return string The cleaned string
     */
    private function cleanString(string $value): string
    {
        // Convert Korean encoding (EUC-KR/CP949) to UTF-8 if needed
        $value = $this->convertToUtf8($value);

        // Handle Lua escape sequences
        $value = str_replace(['\\n', '\\r', '\\t'], ["\n", "\r", "\t"], $value);

        // Convert color codes like ^FFFFFF to HTML spans
        $value = preg_replace('/\^([0-9A-Fa-f]{6})/', '<span style="color:#$1">', $value);

        // Close unclosed spans at end of string if there were color codes
        if (str_contains($value, '<span')) {
            // Count opening and closing tags
            $openCount = substr_count($value, '<span');
            $closeCount = substr_count($value, '</span>');
            // Add missing closing tags
            $value .= str_repeat('</span>', $openCount - $closeCount);
        }

        return trim($value);
    }

    /**
     * Convert string to UTF-8, handling Korean EUC-KR/CP949 encoding.
     *
     * @param  string  $value  The raw string (possibly Korean-encoded)
     * @return string UTF-8 encoded string
     */
    private function convertToUtf8(string $value): string
    {
        // If already valid UTF-8, return as-is
        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        // Try CP949 (Korean Windows codepage, superset of EUC-KR)
        $converted = @mb_convert_encoding($value, 'UTF-8', 'CP949');
        if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
            return $converted;
        }

        // Fallback: strip non-UTF8 characters
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }
}
