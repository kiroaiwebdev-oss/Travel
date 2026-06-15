<?php
// Generates maskable PNG app icons (gradient + paper-plane) for the PWA.
function makeIcon(int $N, string $out): void {
    $im = imagecreatetruecolor($N, $N);
    imagesavealpha($im, true);
    // Diagonal gradient: #0F62FE -> #00B8A9
    [$r1,$g1,$b1] = [0x0F,0x62,0xFE];
    [$r2,$g2,$b2] = [0x00,0xB8,0xA9];
    for ($y = 0; $y < $N; $y++) {
        for ($x = 0; $x < $N; $x++) {
            $t = (($x + $y) / (2 * $N));
            $r = (int)($r1 + ($r2 - $r1) * $t);
            $g = (int)($g1 + ($g2 - $g1) * $t);
            $b = (int)($b1 + ($b2 - $b1) * $t);
            imagesetpixel($im, $x, $y, imagecolorallocate($im, $r, $g, $b));
        }
    }
    $white = imagecolorallocate($im, 255, 255, 255);
    // Paper plane pointing up-right
    $p = fn($fx,$fy) => [(int)($fx*$N),(int)($fy*$N)];
    $body = array_merge($p(.80,.20),$p(.20,.48),$p(.46,.55));
    $wing = array_merge($p(.80,.20),$p(.46,.55),$p(.55,.82));
    if (PHP_VERSION_ID >= 80100) {
        imagefilledpolygon($im, $body, $white);
        imagefilledpolygon($im, $wing, $white);
    } else {
        imagefilledpolygon($im, $body, 3, $white);
        imagefilledpolygon($im, $wing, 3, $white);
    }
    imagepng($im, $out);
    imagedestroy($im);
}
makeIcon(512, __DIR__.'/../backend/public/icons/icon-512.png');
makeIcon(192, __DIR__.'/../backend/public/icons/icon-192.png');
echo "icons generated\n";
