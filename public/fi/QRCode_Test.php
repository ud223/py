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
        $("#qrcode").qrcode({
          render: 'canvas',// render method: 'canvas' or 'div'
          width: 200,
          height: 200,
          color: '#3a3',// QR code color
          bgColor: null,// background color, null for transparent background
          text: 'http://qrcode'// the encoded text

        });
        </script>
    </body>
</html>
