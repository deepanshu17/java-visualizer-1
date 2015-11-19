<?php
    $target_dir = "upload/";
    $filebase = md5(time()).".java";
    $target_file = $target_dir.basename($filebase);
    $txt = $_POST["codeToUpload"];
    $myfile = fopen($target_file, "w");
    fwrite($myfile, $txt);
    fclose($myfile);
    echo $filebase;
?>