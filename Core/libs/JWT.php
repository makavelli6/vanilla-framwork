<?php
class JWT{
    private static function base64UrlEncode($url_string){
        return str_replace(
            ['+', '/', '='],['-', '_', ''],base64_encode($url_string)
        );
    }
    
    public static function generateKey(){
        return bin2hex(random_bytes(32));
    }
    
    private static function getHeader()
    {// Create the token header
        return json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);
    }
    
    public static function generateToken($data = []){
        // Provide standard defaults if not explicitly set
        if (!isset($data['exp'])) {
             // Default expiration 1 hour from now
             $data['exp'] = time() + 3600; 
        }

        $header = self::getHeader();
        // Create the token payload
        $payload = json_encode($data);
        
        // Encode Header
        $base64UrlHeader = self::base64UrlEncode($header);

        // Encode Payload
        $base64UrlPayload = self::base64UrlEncode($payload);

        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, Config::get('ENC_KEY'), true);

        // Encode Signature to Base64Url String
        $base64UrlSignature = self::base64UrlEncode($signature);

        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        return $jwt;
    }

    public static function validateToken($jwt){
        $tokenParts = explode('.', $jwt);
        
        if (count($tokenParts) !== 3) {
            return ['result'=>'error', 'msg' =>'Malformed token structure.'];
        }
        
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];
        
        $payloadData = json_decode($payload, true);

        // check the expiration time natively via time()
        $tokenExpired = false;
        if (isset($payloadData['exp'])) {
            if (time() > $payloadData['exp']) {
                $tokenExpired = true;
            }
        }

        // build a signature based on the header and payload using the secret
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, Config::get('ENC_KEY'), true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        // verify it matches the signature provided in the token
        $signatureValid = ($base64UrlSignature === $signatureProvided);

        if ($tokenExpired) {
            return ['result'=>'error', 'msg' =>'Token has expired.'];
        }
        
        if ($signatureValid) {
            return ['result'=>'success', 'msg' =>'Token is valid.', 'data' => $payloadData];
        } else {
            return ['result'=>'error', 'msg' =>'Token signature is invalid.'];
        }
    }
}




