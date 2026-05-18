<?php
require "remember.php";

if(isset($_SESSION['user_id'])){
    header("Location: profile.php");
} else {
    header("Location: login.php");
}