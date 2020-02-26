<?php
/**
 *  Copyright (C) k1d1412, Inc - All Rights Reserved
 *  @author      k1d1412 <me@1412.io>
 *  @site        https://1412.dev
 *  @date        2/26/20, 8:17 PM
 */
header('Content-Type: application/json');
require_once ('function.php');
$username = $_GET['username'];
$password = $_GET['password'];
$vcbFunction = new VCBFunction();
if ($username != "" && $password != "")
{
    echo $vcbFunction->getLogs($username, $password);
}
else
{
    echo json_encode(array(
        "status" => "error",
        "msg" => "Username/Password invalid !!!"
    ));
}