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
        <form ENCTYPE="multipart/form-data" ACTION="oss-receiver.php" METHOD="POST">
            <input type="hidden" name="MAX_FILE_SIZE"  value="102400000">
            <input NAME="file" TYPE="file">
            <input TYPE="submit" style="display:none" VALUE="Send File">
        </form>
    </body>
</html>
