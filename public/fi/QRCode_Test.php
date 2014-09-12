<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        ?>
        <script type="text/javascript" src="/fi/js/jquery.min.js"></script>
        <script type="text/javascript" src="/fi/js/jquery.qrcode.min.js"></script>
        <div id="qrcode"></div>
        <script type="text/javascript">
//        $("#qrcode").qrcode({
//          render: 'canvas',// render method: 'canvas' or 'div'
//          width: 1000,
//          height: 1000,
//          color: '#3a3',// QR code color
//          bgColor: null,// background color, null for transparent background
//          text: '53e34c0bb3580ee10f8b45df;53e34c05b3580ee10f8b45de;53e34be7b3580ee10f8b45da;53e34be2b3580ee10f8b45d9;53e34bdab3580ee10f8b45d8;53e34bd4b3580ee10f8b45d7;53e34b67b3580ee10f8b45d2;53e34b5fb3580ee10f8b45d1;53e34b59b3580ee10f8b45d0;53e34b35b3580ee10f8b45cb;53e34b2db3580ee10f8b45ca;53e34b25b3580ee10f8b45c9'// the encoded text
//
//        });
        function utf16to8(str) {  
            var out, i, len, c;  
            out = "";  
            len = str.length;  
            for (i = 0; i < len; i++) {  
                c = str.charCodeAt(i);  
                if ((c >= 0x0001) && (c <= 0x007F)) {  
                    out += str.charAt(i);  
                } else if (c > 0x07FF) {  
                    out += String.fromCharCode(0xE0 | ((c >> 12) & 0x0F));  
                    out += String.fromCharCode(0x80 | ((c >> 6) & 0x3F));  
                    out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F));  
                } else {  
                    out += String.fromCharCode(0xC0 | ((c >> 6) & 0x1F));  
                    out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F));  
                }  
            }  
            return out;  
        }  
        $(function () {  
            jQuery('#output').qrcode(utf16to8("e10f8b45d7;53e34b67b3580ee10f8b45d2;53e34b5fb3580ee10f8b45d1;53e34b59b3580ee10f8b45d0;53e34b35b3580ee10f8b45cb;53e34b2db3580ee10f8b45ca;53e34b25b3580ee10f8b45c9"));  
        })  
        </script>
    </body>
</html>
