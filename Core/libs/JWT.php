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
    public static function generateToken($data = ['user_id' => 1,'role' => 'admin','exp' => 1593828222]){
        $header = JWT::getHeader();
        // Create the token payload
        $payload = json_encode($data);
        // Encode Header
        $base64UrlHeader = base64UrlEncode($header);

        // Encode Payload
        $base64UrlPayload = base64UrlEncode($payload);

        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, SECRET, true);

        // Encode Signature to Base64Url String
        $base64UrlSignature = base64UrlEncode($signature);

        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        echo json_encode(['access_token'=>$jwt]);

    }

    public static function validateToken( $jwt)
    {
        // split the token
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];

        // check the expiration time
        //$expiration = Carbon::createFromTimestamp(json_decode($payload)->exp);
        //$tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);

        // build a signature based on the header and payload using the secret
        $base64UrlHeader = base64UrlEncode($header);
        $base64UrlPayload = base64UrlEncode($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = base64UrlEncode($signature);

        // verify it matches the signature provided in the token
        $signatureValid = ($base64UrlSignature === $signatureProvided);

        if ($tokenExpired) {
            echo json_encode(['result'=>'error', 'msg' =>'Token has expired.']);
            return;
        }
        if ($signatureValid) {
            echo json_encode(['result'=>'success', 'msg' =>'Token is valid.']);
        } else {
            echo json_encode(['result'=>'error', 'msg' =>'Token is invalid.']);
        }
    }
}




