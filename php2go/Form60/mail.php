<?php
$to = "yourplace@somewhere.com";
$subject = "My email test.";
$message = "Hello, how are you?";

$headers = "From: helen@christopherstewart.com\r\n";
$headers .= "Reply-To: helen@christopherstewart.com\r\n";
$headers .= "Return-Path: helen@christopherstewart.com\r\n";
$headers .= "CC: helen@christopherstewart.com\r\n";
$headers .= "BCC: helen@christopherstewart.com\r\n";

if ( mail($to,$subject,$message,$headers) ) {
   echo "The email has been sent!";
   } else {
   echo "The email has failed!";
   }
?>
