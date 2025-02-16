<?php

$palette = array(
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

/**
 * Display the colour as a 100 x 100 div.
 * 
 * @parap string|array $colour The colour to be displayed.
 * @return void
 */
function colour_display($colour)
{
    if(is_array($colour)) $colour = colour_dec_to_hex($colour);
    echo '<div style="background-color: '.$colour.'; width: 100px; height: 100px;"></div>';

}

/**
 * Converts a hexadecimal color code to an RGB array.
 *
 * @param string $colour A hexadecimal color code (e.g., "#ff5733" or "ff5733").
 * @return array An array with three integers representing the RGB values [red, green, blue].
 */
function colour_hex_to_dec($colour)
{

    $colour = str_replace('#', '', $colour);

    return array( 
        hexdec(substr($colour, 0, 2)),
        hexdec(substr($colour, 2, 2)),
        hexdec(substr($colour, 4, 2))
    );

}

/**
 * Converts an RGB color from decimal to hexadecimal format.
 *
 * @param array $colour An array with three integers (red, green, blue).
 * @return string The hexadecimal color code (e.g., "#ff5733").
 */
function colour_dec_to_hex($colour)
{

    $colour = array_values($colour);

    return '#' .
        str_pad(dechex($colour[0]), 2, '0', STR_PAD_LEFT) .
        str_pad(dechex($colour[1]), 2, '0', STR_PAD_LEFT) .
        str_pad(dechex($colour[2]), 2, '0', STR_PAD_LEFT);

}

/**
 * Calculates the Euclidean distance between two colors in RGB space.
 *
 * @param string|array $colour_1 The first color as a hex string (e.g., "#ff5733") or an RGB array [red, green, blue].
 * @param string|array $colour_2 The second color as a hex string (e.g., "#33ff57") or an RGB array [red, green, blue].
 * @return float The Euclidean distance between the two colors.
 */
function colour_distance($colour_1, $colour_2)
{

    if(!is_array($colour_1))
    {
        $colour_1 = colour_hex_to_dec($colour_1);
    }
    if(!is_array($colour_2))
    {
        $colour_2 = colour_hex_to_dec($colour_2);
    }

    $colour_1 = array_values($colour_1);
    $colour_2 = array_values($colour_2);

    $delta_r = $colour_1[0] - $colour_2[0];
    $delta_g = $colour_1[1] - $colour_2[1];
    $delta_b = $colour_1[2] - $colour_2[2];

    $distance = sqrt($delta_r * $delta_r + $delta_g * $delta_g + $delta_b * $delta_b);

    return $distance;

}
/**
 * Finds the closest matching color from a predefined list based on RGB distance.
 *
 * @param array|string $target The target color as an RGB array [red, green, blue] or a hex string (e.g., "#ff5733").
 * @param array $colours An array of available colors in RGB format.
 * @return array The closest matching color as an RGB array.
 */
function closest_color($target, $colours)
{

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

/**
 * Generates and displays a color palette as a 1-pixel-high image.
 *
 * @param array $colours An array of hexadecimal color codes (e.g., ["#ff5733", "#33ff57"]).
 * @return void Outputs a base64-encoded GIF image.
 */
function display_palette($colours)
{

    $palette = imagecreatetruecolor(count($colours), 1);

    foreach($colours as $key => $colour)
    {

        $red = hexdec(substr($colour, 1, 2));
        $green = hexdec(substr($colour, 3, 2));
        $blue = hexdec(substr($colour, 5, 2));

        $colour = imagecolorallocate($palette, $red, $green, $blue); 
        imagesetpixel($palette, $key, 0, $colour);

    }

    ob_start (); 
    imagegif($palette);
    $image_data = base64_encode(ob_get_contents()); 
    ob_end_clean (); 

    echo '<img src="data:image/gif;base64, '.$image_data.'" height="100">';

}

/**
 * Converts an image to a 16x16 palette-mapped version using the closest predefined colors.
 *
 * @param string $source_path Path to the source GIF image.
 * @return void Outputs a base64-encoded GIF image.
 */
function image_to_palette($source, $palette)
{

    $source = imagecreatefromgif($source);

    $source_w = imagesx($source);
    $source_h = imagesy($source);

    $destination_image = imagecreate($source_w, $source_h);

    $converted = imagecreatetruecolor(16, 16);

    for($x = 0; $x < $source_w; $x ++)
    {

        for($y = 0; $y < $source_h; $y ++)
        {

            $colour = imagecolorat($source, $y, $x);
            $colour = imagecolorsforindex($source, $colour);

            $colour = closest_color($colour, $palette);

            $red = hexdec(substr($colour, 1, 2));
            $green = hexdec(substr($colour, 3, 2));
            $blue = hexdec(substr($colour, 5, 2));
        
            $colour = imagecolorallocate($converted, $red, $green, $blue); 
            imagesetpixel($converted, $y, $x, $colour);

        }

    }

    ob_start (); 
    imagegif($converted);
    $image_data = base64_encode(ob_get_contents()); 
    ob_end_clean (); 

    echo '<img src="data:image/gif;base64, '.$image_data.'" height="200">';

}

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
    
    <img src="bird-pixelated.png" width="200">
    <img src="bird-converted.png" width="200">
    <?php image_to_palette('bird-pixelated.png', $palette); ?>

</body>
</html>