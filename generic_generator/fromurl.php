<?php

    if(!isset($_GET['url'])) die();
    $url = $_GET['url']; // 'https://i.imgur.com/xWmnzhC.png'

    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    // if seed is not set in the url use a random one!
    if(!$id) mt_srand();
    else mt_srand($id);
    
    // get pixel size (resolution multiplier) default is x1
    $pixel_size = isset($_GET['ps']) ? $_GET['ps'] : 1;
    // logo size
    $width = 120;
    $height = 40;
    list($width, $height) = getimagesize($url);
    // create the "canvas"
    $img = imagecreatetruecolor($width, $height);
    
    // pattern design will have pixel size x1 or x2
    $mod_size = !mt_rand(0, 4) ? 2 : 1; // one on four can have mod_size = 2
    
    $col_min = 10;// min color value
    $col_max = 240;// max color value
    
    for( $c = 0; $c < 3; $c++ )
    {
        // generate background color
        $col_back_R = mt_rand($col_min, $col_max);
        $col_back_G = mt_rand($col_min, $col_max);
        $col_back_B = mt_rand($col_min, $col_max);
        $color_background[$c] = imagecolorallocate($img, $col_back_R, $col_back_G, $col_back_B);
        // generate foreground color
        $col_fore_R = mt_rand($col_min, $col_max);
        $col_fore_G = mt_rand($col_min, $col_max);
        $col_fore_B = mt_rand($col_min, $col_max);
        $color_foreground[$c] = imagecolorallocate($img, $col_fore_R, $col_fore_G, $col_fore_B);
    }

    for( $c = 0; $c < 3; $c++ )
    {
        // pattern matrix, max: 6*8 (x*y)
        $pattern_matrix[$c] = array(
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
                    $pattern_matrix[$c][$x][$y] = $pattern_matrix[$c][$x][$mat_h - $y];
                }

                if( $mirror_vertical && $x > $mat_w / 2 )
                {
                    $pixel_mirrored = true;
                    $pattern_matrix[$c][$x][$y] = $pattern_matrix[$c][$mat_w - $x][$y];
                }

                if( !$pixel_mirrored && mt_rand(0, 1) )
                {
                    $pattern_matrix[$c][$x][$y] = 1;
                }
            }
        }
    }

    function imagecreatefromfile( $filename ) {
        // if (!file_exists($filename)) {
        //     throw new InvalidArgumentException('File "'.$filename.'" not found.');
        // }
        switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ))) {
            case 'jpeg':
            case 'jpg':
                return imagecreatefromjpeg($filename);
            break;

            case 'png':
                return imagecreatefrompng($filename);
            break;

            case 'gif':
                return imagecreatefromgif($filename);
            break;

            default:
                throw new InvalidArgumentException('File "'.$filename.'" is not valid jpg, png or gif image.');
            break;
        }
    }
    // load mask image
    $mask = imagecreatefromfile($url);

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

    function GetChannel($c){
        switch ($c) {
            case 0: return 'red';
            case 1: return 'green';
            case 2: return 'blue';
            default: return 'alpha';
        }
    }

    // apply mask
    for( $x = 0; $x < $width; $x++ )
    {
        for( $y = 0; $y < $height; $y++ )
        {
            // use mask to choose wich pixels to write on the final image
            if(imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) )['alpha'] == 0)
            {
                $mask_col = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );

                $out_col['red'] = 0;
                $out_col['green'] = 0;
                $out_col['blue'] = 0;
                $col_amnt = 0;

                for( $c = 0; $c < 3; $c++ )
                {
                    // apply mask: using bg or fore colors
                    if($pattern_matrix[$c][($x/$mod_size)%$mat_w][($y/$mod_size)%$mat_h] == 1)
                    {
                        $out_col['red'] += imagecolorsforindex($img, $color_foreground[$c])['red'] * ($mask_col[GetChannel($c)]/255);
                        $out_col['green'] += imagecolorsforindex($img, $color_foreground[$c])['green'] * ($mask_col[GetChannel($c)]/255);
                        $out_col['blue'] += imagecolorsforindex($img, $color_foreground[$c])['blue'] * ($mask_col[GetChannel($c)]/255);
                    }else{
                        $out_col['red'] += imagecolorsforindex($img, $color_background[$c])['red'] * ($mask_col[GetChannel($c)]/255);
                        $out_col['green'] += imagecolorsforindex($img, $color_background[$c])['green'] * ($mask_col[GetChannel($c)]/255);
                        $out_col['blue'] += imagecolorsforindex($img, $color_background[$c])['blue'] * ($mask_col[GetChannel($c)]/255);
                    }

                    $col_amnt += $mask_col[GetChannel($c)];
                }
                
                // for( $c = 0; $c < 3; $c++ )  $out_col[GetChannel($c)] /= $col_amnt/255;
                
                imagesetpixel( $img_masked, $x, $y,  imagecolorallocate($img_masked,
                    $out_col['red'],
                    $out_col['green'],
                    $out_col['blue'])
            );
                        
                // apply shades
                $color_masked = imagecolorsforindex( $img_masked, imagecolorat( $img_masked, $x, $y ) );
                $color_border = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
                // $alphaz = (255 - $color_border[GetChannel($c)]);
                // imagesetpixel( $img_masked, $x, $y, imagecolorallocate(
                //     $img_masked,
                //     max(0, $color_masked[ 'red' ] - $alphaz),
                //     max(0, $color_masked[ 'green' ] - $alphaz),
                //     max(0, $color_masked[ 'blue' ] - $alphaz)
                // ) );
            }
            else
            {
                // removed pixels outside the mask
                imagesetpixel( $img_masked, $x, $y, $transparent_background);
            }
        }
    }

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
    imagedestroy($img_masked);
