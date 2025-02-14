<?php

$colours = array(
    '#B3D7D1',
    '#DD982E',
    '#AD6140',
    '#c01111',
    '#F47B30',
    '#E0E0E0',
    '#184632',
    '#B3D7D1',
    '#A0BCAC',
    '#923978',
    '#F785B1',
    '#61AFFF'
);

function colour_hex_to_dec($colour)
{

    return array( 
        hexdec(substr($colour, 1, 2)),
        hexdec(substr($colour, 3, 2)),
        hexdec(substr($colour, 5, 2))
    );
}

function compare_colors($colorA, $colorB) 
{

$colorA = array_values($colorA);
$colorB = array_values($colorB);

    // print_r($colorA);
    // echo '<br>';
    // print_r($colorB);
    // echo '<div style="background-color: rgb('.implode(',', $colorA).'); width: 100px; height: 100px;"></div>';
    // echo '<div style="background-color: rgb('.implode(',', $colorB).'); width: 100px; height: 100px;"></div>';
    
    $difference = abs($colorA[0] - $colorB[0]) + 
        abs($colorA[1] - $colorB[1]) + 
        abs($colorA[2] - $colorB[2]);

    // echo 'Dif: '.$difference;
    // echo '<hr>';

    return $difference;
}

function closest_color($target)
{

    global $colours;

    // $target = colour_hex_to_dec($target);

    print_r($target);
    $selected_color = $colours[0];
    $deviation = PHP_INT_MAX;

    foreach ($colours as $colour) 
    {

        $colour = colour_hex_to_dec($colour);

        $current_deviation = compare_colors($target, $colour);

        // echo $current_deviation.' - '.$deviation;
        // echo '<br>';

        if ($current_deviation < $deviation) 
        {
            $deviation = $current_deviation;
            $selected_color = $colour;
        }

    }

    return $selected_color;

}


// $closest = closest_color('#F47A20');
// echo '<div style="background-color: #F47A20; width: 100px; height: 100px;"></div>';
// echo '<div style="background-color: rgb('.implode(',', $closest).'); width: 100px; height: 100px;"></div>';

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convert Image to Pixelated Image</title>

    <style>

    img {
        image-rendering: pixelated;
    }

    </style>

</head>
<body>
    
    <?php




$palette = imagecreatetruecolor(count($colours), 1);

foreach($colours as $key => $colour)
{
    $red = hexdec(substr($colour, 1, 2));
    $green = hexdec(substr($colour, 3, 2));
    $blue = hexdec(substr($colour, 5, 2));

    $colour = imagecolorallocate($palette, $red, $green, $blue); 
    imagesetpixel($palette, round($key), 0, $colour);
}

ob_start (); 
imagegif($palette);
$image_data = base64_encode(ob_get_contents()); 
ob_end_clean (); 

echo '<img src="data:image/gif;base64, '.$image_data.'" height="100">';

$source = imagecreatefromgif('bird-pixelated.png');

$source_w = imagesx($source);
$source_h = imagesy($source);

$destination_image = imagecreate($source_w, $source_h);

echo '<table>';

for($x = 0; $x < $source_w; $x ++)
{
    echo '<tr>';
    for($y = 0; $y < $source_h; $y ++)
    {

        
        $rgb = imagecolorat($source, $y, $x);

        /*
        echo $rgb.'<br>';

        $r = ($rgb >> 16) & 0xFF;
$g = ($rgb >> 8) & 0xFF;
$b = $rgb & 0xFF;

var_dump($r, $g, $b);
echo '<br>';
*/

$colors = imagecolorsforindex($source, $rgb);

var_dump($colors);

$colors = closest_color($colors);

echo '<br>';
        // $orgb = imagecolorallocate($om,$colors['alpha'],$colors['alpha'],$colors['alpha']);
        // imagesetpixel($destination_image,$x,$y,);

        echo '<td style="
            width: 20px; 
            height: 20px; 
            background-color: rgb(
                '.$colors['0'].',
                '.$colors['1'].',
                '.$colors['2'].');"></td>';

    }

    echo '</tr>';
}

echo '</table>';

/*

// imagepalettecopy($destination_image, $palette);

imagecopy($destination_image, $source, 0, 0, 0, 0, $source_w, $source_h);

imagegif($destination_image, 'bird-converted.png');
imagedestroy($destination_image);
*/

echo '<br>';
echo '<img src="bird-pixelated.png" width="200">';  
echo '&nbsp;';
echo '<img src="bird-converted.png" width="200">';

