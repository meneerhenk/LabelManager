# LabelManager
Labelmanager label maker

This is for the dymo labelmanager wifi, an easy quick and very dirty php interface.


PROOF OF CONCEPT - DO NOT USE IN PRODUCTION

Uses phpqrcode with a small hack:
  public static function image
  in qrimage.php
  returns $target_image;

Add .ttf files to have fonts.

Actually you send hex:
1b 42 to jump to the left (to skip sending empty lines)
1b 44 <len> 16 <bytes> <as> <specified> <in> <len> 16 <next line> 16 <next line>
1b 44 00 16 16 16 16 16 prints empty lines
1b 45 end the print sequence and starts printing 
1b 51 cuts the thingy (maybe these last 2 are reversed)
