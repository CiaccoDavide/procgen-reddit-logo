<?php
    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    // if seed is not set in the url use a random one!
    if(!$id) mt_srand();
    else mt_srand($id);
    
    // get pixel size (resolution multiplier) default is x1
    $pixel_size = isset($_GET['ps']) ? $_GET['ps'] : 1;
    // logo size
    $width = 120;
    $height = 40;
    // create the "canvas"
    $img = imagecreatetruecolor($width, $height);
    
    // pattern design will have pixel size x1 or x2
    $mod_size = !mt_rand(0, 4) ? 2 : 1; // one on four can have mod_size = 2
    
    $col_min = 90;// min color value
    $col_max = 240;// max color value

    // generate background color
    $col_back_R = mt_rand($col_min, $col_max);
    $col_back_G = mt_rand($col_min, $col_max);
    $col_back_B = mt_rand($col_min, $col_max);
    $color_background = imagecolorallocate($img, $col_back_R, $col_back_G, $col_back_B);
    // generate foreground color
    $col_fore_R = mt_rand($col_min, $col_max);
    $col_fore_G = mt_rand($col_min, $col_max);
    $col_fore_B = mt_rand($col_min, $col_max);
    $color_foreground = imagecolorallocate($img, $col_fore_R, $col_fore_G, $col_fore_B);
    // generate eyes color
    $col_eyes_R = min(255, 255 + $col_min - (($col_back_R + $col_fore_R) / 2));
    $col_eyes_G = min(255, 255 + $col_min - (($col_back_G + $col_fore_G) / 2));
    $col_eyes_B = min(255, 255 + $col_min - (($col_back_B + $col_fore_B) / 2));
    $color_eyes = imagecolorallocate($img, $col_eyes_R, $col_eyes_G, $col_eyes_B);
    // calculate eyes shadow color
    $eyes_shadow = 160;
    $color_eyes_shadow = imagecolorallocate($img,
        max(0, $col_eyes_R - $eyes_shadow),
        max(0, $col_eyes_G - $eyes_shadow),
        max(0, $col_eyes_B - $eyes_shadow)
    );

    // backround color
    imagefilledrectangle($img, 0, 0, $width, $height, $color_background);
    imagecolortransparent($img, $color_background); // toggle transparent background

    $eyes = array(
        // left eye
        0 => array(11,13),
        1 => array(12,13),
        2 => array(11,14),
        3 => array(12,14),
        // right eye
        4 => array(19,13),
        5 => array(20,13),
        6 => array(19,14),
        7 => array(20,14),
    );
    $eyes_shadow = array(
        // left eye
        0 => array(11,12),
        1 => array(12,12),
        2 => array(11,15),
        3 => array(12,15),
        4 => array(10,13),
        5 => array(10,14),
        6 => array(13,13),
        7 => array(13,14),
        // right eye
        8 => array(19,12),
        9 => array(20,12),
        10 => array(19,15),
        11 => array(20,15),
        12 => array(18,13),
        13 => array(18,14),
        14 => array(21,13),
        15 => array(21,14),
    );

    // pattern matrix, max: 6*8 (x*y)
    $pattern_matrix = array(
        0 => array(0,0,0,0,0,0),
        1 => array(0,0,0,0,0,0),
        2 => array(0,0,0,0,0,0),
        3 => array(0,0,0,0,0,0),
        4 => array(0,0,0,0,0,0),
        5 => array(0,0,0,0,0,0),
        6 => array(0,0,0,0,0,0),
        7 => array(0,0,0,0,0,0),
    );

    // set pattern matrix size and properties
    $mat_w = mt_rand(3, 8);
    $mat_h = mt_rand(3, 6);
    $mirror_horizontal = mt_rand(0, 1);
    $mirror_vertical = mt_rand(0, 1);
    $pixel_mirrored = false;

    // generate the pattern matrix
    for( $x = 0; $x < $mat_w; $x++ )
    {
        for( $y = 0; $y < $mat_h; $y++ )
        {
            $pixel_mirrored = false;

            if( $mirror_horizontal && $y > $mat_h / 2 )
            {
                $pixel_mirrored = true;
                $pattern_matrix[$x][$y] = $pattern_matrix[$x][$mat_h - $y];
            }

            if( $mirror_vertical && $x > $mat_w / 2 )
            {
                $pixel_mirrored = true;
                $pattern_matrix[$x][$y] = $pattern_matrix[$mat_w - $x][$y];
            }

            if( !$pixel_mirrored && mt_rand(0, 1) )
            {
                $pattern_matrix[$x][$y] = 1;
            }
        }
    }

    // load mask image
    $mask = imagecreatefrompng('img/mask.png');
    // load shades image
    $border = imagecreatefrompng('img/greyscale_borders.png');
    // TODO: hardcode the mask and the shades inside an array (like the eyes one)

    // create masked image
    $img_masked = imagecreatetruecolor($width, $height);

    // make $img_masked a transparent image
    // turning off alpha blending (to ensure alpha channel information is preserved, rather than removed (blending with the rest of the image in the form of black))
    imagealphablending($img_masked, false);
    // turning on alpha channel information saving (to ensure the full range of transparency is preserved)
    imagesavealpha($img_masked, true);
    // transparent background (will be removed after line 185)
    $transparent_background = imagecolorallocatealpha( $img_masked, 1, 2, 3, 127 );
    imagefilledrectangle($img_masked, 0, 0, $width, $height, $transparent_background);



    // apply mask
    for( $x = 0; $x < $width; $x++ )
    {
        for( $y = 0; $y < $height; $y++ )
        {
            // use mask to choose wich pixels to write on the final image
            if(imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) )['alpha'] == 0)
            {
                // apply mask: using bg or fore colors
                if($pattern_matrix[($x/$mod_size)%$mat_w][($y/$mod_size)%$mat_h] == 1) imagesetpixel( $img_masked, $x, $y, $color_foreground);
                else imagesetpixel( $img_masked, $x, $y, imagecolorat( $img, $x, $y ) );

                // apply shades
                $color_masked = imagecolorsforindex( $img_masked, imagecolorat( $img_masked, $x, $y ) );
                $color_border = imagecolorsforindex( $border, imagecolorat( $border, $x, $y ) );
                $alphaz = (255 - $color_border['red']);
                imagesetpixel( $img_masked, $x, $y, imagecolorallocate(
                    $img_masked,
                    max(0, $color_masked[ 'red' ] - $alphaz),
                    max(0, $color_masked[ 'green' ] - $alphaz),
                    max(0, $color_masked[ 'blue' ] - $alphaz)
                ) );
            }
            else
            {
                // removed pixels outside the mask
                imagesetpixel( $img_masked, $x, $y, $transparent_background);
            }
        }
    }

    // draw the eyes on the masked image
    for( $e = 0; $e < count($eyes); $e++ ) imagesetpixel( $img_masked, $eyes[$e][0], $eyes[$e][1], $color_eyes);
    // draw the shadow around the eyes
    for( $e = 0; $e < count($eyes_shadow); $e++ ) imagesetpixel( $img_masked, $eyes_shadow[$e][0], $eyes_shadow[$e][1], $color_eyes_shadow);

    // apply resolution multiplier (pixel size)
    $img_masked = imagescale($img_masked, $width * $pixel_size, $height * $pixel_size, IMG_NEAREST_NEIGHBOUR);

    // removing the black from the placeholder
    imagecolortransparent($img_masked, $transparent_background);

    // set content header: this file will not load like a normal html page, it will output a png file
    header("Content-type: image/png");
    // output image
    imagepng($img_masked);
    // destroy images
    imagedestroy($img);
    imagedestroy($mask);
    imagedestroy($border);
    imagedestroy($img_masked);
