<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class CreateEmblemFromData
{
    use AsAction;

    public function handle(string $bmpData)
    {
        $header = unpack('vtype/Vsize/v2reserved/Voffset', substr($bmpData, 0, 14));
        $info = unpack('Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant', substr($bmpData, 14, 40));
        extract($info);
        extract($header);
        if ($type != 0x4D42) {
            return false;
        }
        $palette_size = $offset - 54;
        $imres = imagecreatetruecolor($width, $height);
        imagealphablending($imres, false);
        imagesavealpha($imres, true);
        $pal = [];
        if ($palette_size) {
            $palette = substr($bmpData, 54, $palette_size);
            $j = 0;
            $n = 0;
            while ($j < $palette_size) {
                $b = ord($palette[$j++]);
                $g = ord($palette[$j++]);
                $r = ord($palette[$j++]);
                $a = ord($palette[$j++]);
                if (($r & 0xf8 == 0xf8) && ($g == 0) && ($b & 0xf8 == 0xf8)) {
                    $a = 127;
                } // alpha = 255 on 0xFF00FF

                // Clamp each value to its valid range
                $r = max(0, min(255, $r));
                $g = max(0, min(255, $g));
                $b = max(0, min(255, $b));
                $a = max(0, min(127, $a));

                // Allocate the color with the clamped values
                $pal[$n++] = imagecolorallocatealpha($imres, $r, $g, $b, $a);
            }
        }
        $scan_line_size = (($bits * $width) + 7) >> 3;
        $scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size & 0x03) : 0;
        for ($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {
            $scan_line = substr($bmpData, $offset + (($scan_line_size + $scan_line_align) * $l), $scan_line_size);
            if ($bits == 24) {
                $j = 0;
                $n = 0;
                while ($j < $scan_line_size) {
                    $b = ord($scan_line[$j++]);
                    $g = ord($scan_line[$j++]);
                    $r = ord($scan_line[$j++]);
                    $a = 0;
                    if (($r & 0xf8 == 0xf8) && ($g == 0) && ($b & 0xf8 == 0xf8)) {
                        $a = 127;
                    } // alpha = 255 on 0xFF00FF
                    $col = imagecolorallocatealpha($imres, $r, $g, $b, $a);
                    imagesetpixel($imres, $n++, $i, $col);
                }
            } elseif ($bits == 8) {
                $j = 0;
                while ($j < $scan_line_size) {
                    $col = $pal[ord($scan_line[$j++])];
                    imagesetpixel($imres, $j - 1, $i, $col);
                }
            } elseif ($bits == 4) {
                $j = 0;
                $n = 0;
                while ($j < $scan_line_size) {
                    $byte = ord($scan_line[$j++]);
                    $p1 = $byte >> 4;
                    $p2 = $byte & 0x0F;
                    imagesetpixel($imres, $n++, $i, $pal[$p1]);
                    imagesetpixel($imres, $n++, $i, $pal[$p2]);
                }
            } elseif ($bits == 1) {
                $j = 0;
                $n = 0;
                while ($j < $scan_line_size) {
                    $byte = ord($scan_line[$j++]);
                    $p1 = (int) (($byte & 0x80) != 0);
                    $p2 = (int) (($byte & 0x40) != 0);
                    $p3 = (int) (($byte & 0x20) != 0);
                    $p4 = (int) (($byte & 0x10) != 0);
                    $p5 = (int) (($byte & 0x08) != 0);
                    $p6 = (int) (($byte & 0x04) != 0);
                    $p7 = (int) (($byte & 0x02) != 0);
                    $p8 = (int) (($byte & 0x01) != 0);
                    imagesetpixel($imres, $n++, $i, $pal[$p1]);
                    imagesetpixel($imres, $n++, $i, $pal[$p2]);
                    imagesetpixel($imres, $n++, $i, $pal[$p3]);
                    imagesetpixel($imres, $n++, $i, $pal[$p4]);
                    imagesetpixel($imres, $n++, $i, $pal[$p5]);
                    imagesetpixel($imres, $n++, $i, $pal[$p6]);
                    imagesetpixel($imres, $n++, $i, $pal[$p7]);
                    imagesetpixel($imres, $n++, $i, $pal[$p8]);
                }
            }
        }

        return $imres;
    }
}
