<?php
if(!isset($_SESSION['profile'])){
    header("Location: ?tab=logon");
}else{
    echo OutTpl("statistics.tpl");
}