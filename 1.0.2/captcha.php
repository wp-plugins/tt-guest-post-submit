<?php

session_start();

/*$string = '';
 
for ($i = 0; $i < 5; $i++) {
    // this numbers refer to numbers of the ascii table (lower case)
    $string .= chr(rand(97, 122));
    
}
 
$_SESSION['random_code'] = $string;
*/
$dir = 'fonts/';
 
$image = imagecreatetruecolor(100, 50);
$black = imagecolorallocate($image, 0, 0, 0);
$color = imagecolorallocate($image, 200, 100, 90); // red
$white = imagecolorallocate($image, 255, 255, 255);
 
imagefilledrectangle($image,0,0,399,70,$white);
imagettftext ($image, 20, 0, 10, 35, $color, $dir."arial.ttf", $_SESSION['random_code']);



header("Content-type: image/png");
imagepng($image);

?>