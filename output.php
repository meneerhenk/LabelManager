<?php
$size=$_REQUEST["size"];

$pixels=floor($size*11);
$left=round((24-$size)*0.66);

$text1 = $_REQUEST["text1"];
$text2 = $_REQUEST["text2"];
$font1 = $_REQUEST["font1"];
$font2 = $_REQUEST["font2"];
$qrcode = $_REQUEST["qrcode"];

$wplus=0;
if($text2=="" || $qrcode=="yes") {
  if($qrcode=="yes") $wplus=$pixels;
  $height=$pixels;
  $bbox = imagettfbbox($height, 0, $font1, $text1);
  $rh=$bbox[1]+(0-$bbox[5]);
  $scale=$height/$rh;
  if($scale<1) {
    $height=floor($height*$scale);
    $bbox = imagettfbbox($height, 0, $font1, $text1);
  }
  $im = imagecreatetruecolor(ceil($pixels/8)*8, $bbox[2]+30+$wplus);
  $im2 = imagecreatetruecolor($bbox[2]+30+$wplus,ceil($pixels/8)*8);
} else {
  $big1=$_REQUEST["big1"];
  $big2=$_REQUEST["big2"];
  if($big1!="yes" && $big2!="yes") {
    $height1=$pixels*0.45;
    $margin=$pixels*0.1;
    $height2=$pixels*0.45;
  }elseif($big1=="yes") {
    $height1=$pixels*0.63;
    $margin=$pixels*0.06;
    $height2=$pixels*0.33;
  }elseif($big2=="yes") {
    $height1=$pixels*0.33;
    $margin=$pixels*0.06;
    $height2=$pixels*0.63;
  }
  $bbox1 = imagettfbbox($height1, 0, $font1, $text1);
  $rh1=$bbox1[1]+(0-$bbox1[5]);
  $scale1=$height1/$rh1;
  if($scale1<1) {
    $height1=floor($height1*$scale1);
    $bbox1 = imagettfbbox($height1, 0, $font1, $text1);
  }

  $bbox2 = imagettfbbox($height2, 0, $font2, $text2);
  $rh2=$bbox2[1]+(0-$bbox2[5]);
  $scale2=$height2/$rh2;
  if($scale2<1) {
    $height2=floor($height2*$scale2);
    $bbox2 = imagettfbbox($height2, 0, $font2, $text2);
  }

  $bbox=array(  
                max($bbox1[0],$bbox2[0]),
                max($bbox1[1],$bbox2[1]),
                max($bbox1[2],$bbox2[2]),
                max($bbox1[3],$bbox2[3]),
                max($bbox1[4],$bbox2[4]),
                max($bbox1[5],$bbox2[5])
             );
  $im = imagecreatetruecolor($bbox[2]+30,ceil($pixels/8)*8);
  $im2 = imagecreatetruecolor($bbox[2]+30,ceil($pixels/8)*8);
}

$white = imagecolorallocate($im, 255, 255, 255);
$white2 = imagecolorallocate($im, 192, 192, 192);
$black = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im2, 0, 0, imagesx($im2)-1, imagesy($im2)-1, $white);
imagefilledrectangle($im, 0, 0, imagesx($im)-1, imagesy($im)-1, $white);


if($text2=="" || $qrcode=="yes") {
  imagettftext($im2, $height, 0, $bbox[1]+$wplus, 0-$bbox[5], 0-$black, $font1, $text1);
  if($qrcode=="yes") {
    include('./phpqrcode/qrlib.php');
    $qr=QRcode::image($text2,false,QR_ECLEVEL_L,3,0); 
    imagecopyresized($im2,$qr,1,1,0,0,$pixels,$pixels,imagesx($qr),imagesy($qr));

  }
} else {
  imagettftext($im2, $height1, 0, $bbox[1], 0-$bbox1[5], 0-$black, $font1, $text1);
  imagettftext($im2, $height2, 0, $bbox[1], 0-$bbox1[5]-$bbox2[5]+$margin, 0-$black, $font2, $text2);
}

$im=imagerotate($im2,90,$white);
if($_REQUEST["img"]=="true") {
  header('Content-Type: image/png');
  imagepng($im2);
  exit(0);
}
echo "<pre>";
for($y=0;$y<imagesy($im);$y++) {
  $byte=0;
  $bit=7;
  for($x=0;$x<imagesx($im);$x++) {
    if(imagecolorat($im, $x, $y)>(16777215/2)) {
      $line[$y][$byte]+=0<<($bit);
    } else {
      $line[$y][$byte]+=1<<($bit);
    }
    $bit--;
    if($bit<0) { $bit=7; $byte++; }
  }
}
$tosent="";

$skip=-2;
foreach($line as $y => $bytes) {
  $aantal=count($bytes);
  $start=-1;
  $firstbyte=0;
  for($i=($aantal-1);$i>=0;$i--) {
    if($bytes[$i]!=0x00) break;
//    unset($bytes[$i]);
  }
  $aantal=count($bytes);
  if($aantal%2==0) { $bytes[]=0x00; $aantal++; }
  foreach($bytes as $idx => $byte) {
    if($start==-1) {
      if($byte!=0x00) {
        $start=$idx;
        if($skip!=$idx) {
          $tosent.="\x1b\x42".chr($idx+$left)."\x1b\x44".chr($aantal-$idx);
          printf("\n1b 42 %02x\n1b 44 %02x ",$idx+$left,$aantal-$idx);
          $skip=$idx;
        }
      }
    }
    if($firstbyte==0 && $start>-1) {
      $firstbyte=1;
      $tosent.="\x16";
      printf("16 ");
    }
    if($start!=-1) {
        $tosent.=chr($byte);
        printf("%02x ",$byte);
    }
  }
  if($start==-1) {
    if($skip!=-1) {
      $tosent.="\x1b\x44\x00";
      printf("\n1b 44 00 ");
      $skip=-1;
    }
    $tosent.="\x16";
    printf("16 ");
  }
}
//imagewbmp($im, NULL, $white);
//imagepng($im);
imagedestroy($im);
echo "1b 44 00 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16 16
1b 45 
1b 51";
$tosent.="\x1b\x44\x00\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x16\x1b\x45\x1b\x51";

echo base64_encode($tosent);
$fp=fsockopen("172.16.22.139",9100);
fwrite($fp,$tosent);
fclose($fp);


?>


