<?php
class CryptoUtils{
    public const AES_SECRET_KEY = "32digitHk8^&g%fkd0J?d6H&v7d%k#kM"; // 32 digits


    /**
     * ********************************************************************
     *   AES Encrypt
     * ********************************************************************
     */
    public static function AES_Encrypt(?string $secret_key=null , $data){

        // Check secret key
        if($secret_key==null || strlen($secret_key)!=32) $secret_key = self::AES_SECRET_KEY;

        // Encryption method (must be the same as used for encryption)
        $method = "AES-256-CBC";
        

        // Create IV From Secret Key
        $ivLength = openssl_cipher_iv_length($method);
        $iv = substr($secret_key, 0, $ivLength);


        // Encrypt the data
        return openssl_encrypt($data, $method, $secret_key, 0, $iv);

    }

    /**
     * ********************************************************************
     *   AES Decrypt
     * ********************************************************************
     */
    public static function AES_Decrypt(?string $secret_key = null, $encrypted_data) {
        // Check secret key
        if ($secret_key == null || strlen($secret_key) != 32) $secret_key = self::AES_SECRET_KEY;

        // Encryption method (must be the same as used for encryption)
        $method = "AES-256-CBC";

        // Create IV From Secret Key
        $ivLength = openssl_cipher_iv_length($method);
        $iv = substr($secret_key, 0, $ivLength);

        // Decrypt the data
        return openssl_decrypt($encrypted_data, $method, $secret_key, 0, $iv);
    }

    /**
     * ********************************************************************
     *   Hash SHA-256
     * ********************************************************************
     */
    public static function hash($plain_data, $alg = 'SHA256') {
        // Convert the algorithm to uppercase for case-insensitive comparison
        $alg = strtoupper($alg);
    
        // Define supported algorithms
        $supported_algorithms = ['SHA256', 'SHA512', 'MD5'];
    
        // Check if the algorithm is supported
        if (!in_array($alg, $supported_algorithms)) {
            // "Unsupported hashing algorithm: $alg" 
            return null;
        }
    
        // Use the hash function for all algorithms, including MD5
        return hash($alg, $plain_data);
    }
}
