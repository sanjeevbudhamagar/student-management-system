<?php
include '../common/Common.php';

$objCommon = new Common();

if(isset($_FILES['file'])) {

    $file = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    move_uploaded_file($file_tmp, "../Examples/excelFiles/Fee/$file");


    $result = $objCommon->FeeExcel($file,$file_tmp);

}
