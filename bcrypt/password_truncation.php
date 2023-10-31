<?php
// This script demonstrates the need for checking the byte size of a password 
// BEFORE passing it to bcrypt as whatever implementation php uses under the hood
// will truncate the password if it exceeds 72 bytes.
// In this example, I use the UTF-8 character 𐍈 (hwair) which is 4 bytes wide 
// to demonstrate that although the passwords are 18 & 19 characters,
// the bcrypt hashes are the same under the same salt

// avoid warnings about deprecation for salt
error_reporting(E_ERROR | E_PARSE);

// php 8 ignores the salt parameter & generates it
// php7.4 is chosen to avoid this for the POC
$salt = str_repeat("a", 22); // constant for demonstration

$password = str_repeat("𐍈", 18);
$byteLen = strlen($password); // 4*18 = 72, as 𐍈 is 4 bytes wide in UTF-8
$charLen = mb_strlen($password); // 18 as it correctly detects UTF-8

$firstHash = password_hash(
	$password, 
	PASSWORD_BCRYPT, 
	['salt' => $salt]
);

echo "password: $password 
	byte length $byteLen 
	mb character length $charLen
	salt $salt
	hash $firstHash" . PHP_EOL;

// Output:
// password: 𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈 
//	byte length 72 
//	mb character length 18
//	salt aaaaaaaaaaaaaaaaaaaaaa
//	hash $2y$10$aaaaaaaaaaaaaaaaaaaaaORvbfX/WtDya2.AS36By41ACIqzHIshW

$password = str_repeat("𐍈", 19);;
$byteLen = strlen($password); // 4*19 = 76, as 𐍈 is 4 bytes wide in UTF-8
$charLen = mb_strlen($password); // 19 as it correctly detects UTF-8
$secondHash = password_hash(
	$password, 
	PASSWORD_BCRYPT, 
	['salt' => $salt]
);

echo "password: $password 
	byte length $byteLen 
	mb character length $charLen
	salt $salt
	hash $secondHash" . PHP_EOL;

// Output:
// password: 𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈𐍈 
//	byte length 76 
//	mb character length 19
//	salt aaaaaaaaaaaaaaaaaaaaaa
//	hash $2y$10$aaaaaaaaaaaaaaaaaaaaaORvbfX/WtDya2.AS36By41ACIqzHIshW
