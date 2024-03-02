<?php
// set your parameters 
$inputFile = 'input.mp4';
$outputWidth = 300;
$outputHeight = 200;
$frameRate = 10;
$framesDir = 'frames';

// extract input vid to .png so you can convert them to ascii (also I love ffmpeg <3)
// exec("ffmpeg -i $inputFile -vf fps=$frameRate $framesDir/frame%d.png");

$frames = glob("$framesDir/*.png", GLOB_NOSORT | GLOB_BRACE);
natsort($frames);
echo "<html><head><style> body{ font-size:1px;}</style></head>";
echo '<pre id="ascii-frame"></pre>';


// advanced weed programing (I have no idea how I did this)
foreach ($frames as $frame) {
    $image = imagecreatefrompng($frame);
    $frameAscii = '';

    for ($y = 0; $y < $outputHeight; $y++) {
        for ($x = 0; $x < $outputWidth; $x++) {
            $rgb = imagecolorat($image, $x, $y);
            $brightness = (int)((($rgb >> 16) & 0xFF) + (($rgb >> 8) & 0xFF) + ($rgb & 0xFF)) / 3;
            $frameAscii .= getAsciiChar($brightness);
        }
        $frameAscii .= "\n";
    }

    // pls forgive me father for I have sinned 
    echo "<script>document.getElementById('ascii-frame').textContent = `$frameAscii`;</script>";

    usleep(1000000 / $frameRate);
}

foreach ($frames as $frame) {
    unlink($frame);
}
rmdir($framesDir);

error_log("jebło z ekstrakcją klatek: " . print_r(error_get_last(), true));

function getAsciiChar($brightness) {
    $asciiChars = [' ', '.', ':', '-', '=', '+', '*', '#', '%', '@'];
    $charIndex = min((int)($brightness / 25.5), 9);
    return $asciiChars[$charIndex];
}
