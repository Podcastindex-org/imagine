<? include get_cfg_var("global_conf") . '/includes/env.php'; ?>
<? include "$cg_confroot/$cg_templates/php_bin_init.php" ?>
<?


//Globals
$feedRepo = "/data/feed/";
$feedId = trim($argv[1]);
$imageUrl = trim($argv[2]);
$imageUrl = preg_replace('/\?.*/', '', $imageUrl);
$imageFileName = $feedId."_".cleanFilename(basename($imageUrl), TRUE, TRUE);
$feedFile2400 = $feedId."_2400.jpg";
$feedFile1200 = $feedId."_1200.jpg";
$feedFile600 = $feedId."_600.jpg";
$feedFile300 = $feedId."_300.jpg";


//Parameter check
if(empty($feedId) || !is_numeric($feedId)) die("Bad feed id.");
if(stripos($imageUrl, 'http') !== 0) die("Bad image url.");
if(!is_dir($feedRepo.$feedId)) {
    mkdir($feedRepo.$feedId, 777, TRUE);
}


//Download the image
$response = fetchUrlExtra($imageUrl);
if($response['status_code'] > 304 || empty($response['body'])) die("Failed to download the image.");


//Write the image file
$fpcResult = file_put_contents($imageFileName, $response['body']);
if($fpcResult === FALSE || $fpcResult < 10) die("Failed to write the original image file to disk.");


//Resize it to 2400, 1200, 600, 300
echo image_resize($imageFileName, $feedFile2400, "jpg", 2400, NULL, NULL)."\n";
echo image_resize($imageFileName, $feedFile1200, "jpg", 1200, NULL, NULL)."\n";
echo image_resize($imageFileName, $feedFile600, "jpg", 600, NULL, NULL)."\n";
echo image_resize($imageFileName, $feedFile300, "jpg", 300, NULL, NULL)."\n";


//Move to object storage main repository
if(file_exists($feedFile2400)) rename($feedFile2400, "$feedRepo"."$feedId/2400.jpg");
if(file_exists($feedFile1200)) rename($feedFile1200, "$feedRepo"."$feedId/1200.jpg");
if(file_exists($feedFile600)) rename($feedFile600, "$feedRepo"."$feedId/600.jpg");
if(file_exists($feedFile300)) rename($feedFile300, "$feedRepo"."$feedId/300.jpg");




//Functions
function image_resize($src, $dst, $newtype, $width, $height, $crop = 0)
{
    echo "Writing $dst...\n";

    if (!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

    if ($w > $h) {
        $ar = $w / $h;
        $longside = 'w';
    } else {
        $ar = $h / $w;
        $longside = 'h';
    }
    $height = round($width / $ar);

    $type = strtolower(substr(strrchr($src, "."), 1));
    if ($type == 'jpeg') $type = 'jpg';
    switch ($type) {
        case 'bmp':
            $img = imagecreatefromwbmp($src);
            break;
        case 'gif':
            $img = imagecreatefromgif($src);
            break;
        case 'jpg':
            $img = imagecreatefromjpeg($src);
            break;
        case 'png':
            $img = imagecreatefrompng($src);
            break;
        default :
            return "Unsupported picture type!";
    }

    // resize
    if ($crop) {
        if ($w < $width or $h < $height) return "Picture is too small!";
        $ratio = max($width / $w, $height / $h);
        $h = $height / $ratio;
        $x = ($w - $width / $ratio) / 2;
        $w = $width / $ratio;
    } else {
        if ($w < $width and $h < $height) return "Picture is too small!";
        $ratio = min($width / $w, $height / $h);
        $width = $w * $ratio;
        $height = $h * $ratio;
        $x = 0;
    }

    loggit(3, "[$src|$dst|$newtype|$width|$height]");
    $new = imagecreatetruecolor($width, $height);

    // preserve transparency
    if ($type == "gif" or $type == "png") {
        imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
        imagealphablending($new, false);
        imagesavealpha($new, true);
    }

    imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

    switch ($newtype) {
        case 'bmp':
            imagewbmp($new, $dst);
            break;
        case 'gif':
            imagegif($new, $dst);
            break;
        case 'jpg':
            imagejpeg($new, $dst);
            break;
        case 'png':
            imagepng($new, $dst);
            break;
    }
    return true;
}
