<?php

if(!function_exists('verifyAuthToken')) {
    function verifyAuthToken($token){
        $jwt =new JWT();
        $jwtsceret = 'smartailor';
        $verification = $jwt->decode($token,$JwtSceretKey,'HS256');
        
        $verification_json = $jwt->jsonEncode($verification);
        return $verification_json;    
    }
}