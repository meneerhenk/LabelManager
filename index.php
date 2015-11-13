<HTML>
<HEAD>
<TITLE>Label maker</TITLE>
<script src="jquery-1.11.3.min.js"></script>
<script>
function bla() {
  var size=$('#size').val();
  var big1=$('#big1').is(':checked') ? "yes" : "no" ;
  var big2=$('#big2').is(':checked') ? "yes" : "no" ;
  var qrcode=$('#qrcode').is(':checked') ? "yes" : "no" ;
  var font1=$('#font1').val();
  var font2=$('#font2').val();
  var line1=$('#line1').val();
  var line2=$('#line2').val();
  $('#preview').attr("src", "output.php?img=true"+
       "&text1="+encodeURIComponent(line1)+
       "&text2="+encodeURIComponent(line2)+
       "&font1="+font1+
       "&font2="+font2+
       "&big1="+big1+
       "&big2="+big2+
       "&qrcode="+qrcode+
       "&size="+size );
}
function print() {
  var size=$('#size').val();
  var big1=$('#big1').is(':checked') ? "yes" : "no" ;
  var big2=$('#big2').is(':checked') ? "yes" : "no" ;
  var qrcode=$('#qrcode').is(':checked') ? "yes" : "no" ;
  var font1=$('#font1').val();
  var font2=$('#font2').val();
  var line1=$('#line1').val();
  var line2=$('#line2').val();
  $('#printimg').attr("src", "output.php?img=false"+
       "&text1="+encodeURIComponent(line1)+
       "&text2="+encodeURIComponent(line2)+
       "&font1="+font1+
       "&font2="+font2+
       "&big1="+big1+
       "&big2="+big2+
       "&qrcode="+qrcode+
       "&size="+size );
}
$( document ).ready(function() {
  bla();
});
</script>
</HEAD>
<BODY>
<select name="size" id="size" onchange="bla();">
<option value="6">6mm</option>
<option value="9" selected>9mm</option>
<option value="12">12mm</option>
<option value="22">24mm</option>
</select>
<input id="big1" type="checkbox" name="big1" value="yes" onclick="bla();"> Line 1 large
<input id="big2" type="checkbox" name="big2" value="yes" onclick="bla();"> Line 2 large
<select name="font1" id="font1" onchange="bla();">
<?php
  $files=glob("*.ttf");
  include 'ttfInfo.class.php'; 
  foreach($files as $font) {
    $fontinfo = getFontInfo($font); 
    echo "<option value=\"$font\">".$fontinfo[1]." - ".$fontinfo[2]."</option>";
  }
?>
</select>
<select name="font2" id="font2" onchange="bla();">
<?php
  foreach($files as $font) {
    $fontinfo = getFontInfo($font); 
    echo "<option value=\"$font\">".$fontinfo[1]." - ".$fontinfo[2]."</option>";
  }
?>
</select><input type="submit" id="print" name="print" value="Print" onclick="print();">
<br/>
<span class="line1">Line 1 <input id="line1" type="text" name="text1" size=48 onkeyup="bla();"></span><br/>
<span class="line2">Line 2 <input id="line2" type="text" name="text2" size=48 onkeyup="bla();"></span>
<input id="qrcode" type="checkbox" name="qrcode" value="yes" onclick="bla();">QRcode<br/>
Preview:<br/>
<span ><img style="border-style: solid; border-width: 5px;" id="preview" src=""></span><br>
<img id="printimg" src="">

</BODY>
</HTML>
