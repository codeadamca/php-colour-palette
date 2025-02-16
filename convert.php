<?php

$colours = array(
    '#B3D7D1',
    '#DD982E',
    '#AD6140',
    '#c01111',
    '#F47B30',
    '#E0E0E0',
    '#184632',
    '#A0BCAC',
    '#923978',
    '#F785B1',
    '#61AFFF'
);

function colour_display($colour)
{
    if(is_array($colour)) $colour = colour_dec_to_hex($colour);
    echo '<div style="background-color: '.$colour.'; width: 100px; height: 100px;"></div>';

}

function colour_hex_to_dec($colour)
{

    $colour = str_replace('#', '', $colour);

    return array( 
        hexdec(substr($colour, 0, 2)),
        hexdec(substr($colour, 2, 2)),
        hexdec(substr($colour, 4, 2))
    );

}

function colour_dec_to_hex($colour)
{

    $colour = array_values($colour);

    return '#'.
        dechex($colour[0]).
        dechex($colour[1]).
        dechex($colour[2]);

}

function colour_distance($col1, $col2)
{

    echo '<hr>';
    if(!is_array($col1))
    {
        $col1 = colour_hex_to_dec($col1);
    }
    if(!is_array($col2))
    {
        $col2 = colour_hex_to_dec($col2);
    }

    $col1 = array_values($col1);
    $col2 = array_values($col2);

    colour_display($col1);
    colour_display($col2);

    print_r($col1);
    echo '<br>';
    print_r($col2);
    echo '<br>';

    $delta_r = $col1[0] - $col2[0];
    $delta_g = $col1[1] - $col2[1];
    $delta_b = $col1[2] - $col2[2];

    $distance = sqrt($delta_r * $delta_r + $delta_g * $delta_g + $delta_b * $delta_b);

    echo $distance;

    return $distance;

    // return $delta_r * $delta_r + $delta_g * $delta_g + $delta_b * $delta_b;

    // return abs($delta_r) + abs($delta_g) + abs($delta_b);

}

function closest_color($target)
{

    global $colours;

    if(count($target) == 4)
    {
        array_pop($target);
    }

    $selected_color = $colours[0];
    $deviation = PHP_INT_MAX;

    foreach ($colours as $colour) 
    {

        $current_deviation = colour_distance($target, $colour);

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
    imagesetpixel($palette, $key, 0, $colour);

}

// $palette = imagetruecolortopalette($palette, false, 255);

ob_start (); 
imagegif($palette);
$image_data = base64_encode(ob_get_contents()); 
ob_end_clean (); 

echo '<img src="data:image/gif;base64, '.$image_data.'" height="100">';




$source = imagecreatefromgif('bird-pixelated.png');

$source_w = imagesx($source);
$source_h = imagesy($source);

$destination_image = imagecreate($source_w, $source_h);

echo '<table border="1">';

for($x = 0; $x < $source_w; $x ++)
{
    echo '<tr>';

    for($y = 0; $y < $source_h; $y ++)
    {

        $colour = imagecolorat($source, $y, $x);
        $colour = imagecolorsforindex($source, $colour);

        echo '<br>';
        print_r($colour);
        colour_display($colour);

        $colour = closest_color($colour);

        print_r(colour_hex_to_dec($colour));
        colour_display($colour);

        echo '<hr>';    

        echo '<td style="
            width: 20px; 
            height: 20px; 
            background-color: '.$colour.';"></td>';

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

