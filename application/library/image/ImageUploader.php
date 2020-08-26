<?php

namespace app\library\image;

class ImageUploader
{
    public static function upload($file, $file_name, array $dimensions = array(), array $options = array())
    {
        $url = IMAGES_UPLOAD_URL;
        $shared_key = '6154a12c330ec4200d919b03120afd0a';
        $api_key = '8e664516151664bf7897da7095cdf057';


        $cipher = "aes-256-gcm";
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $post_api_key = openssl_encrypt($api_key, $cipher, $shared_key, 0, $iv, $tag);


        $curl = curl_init();

        $cFile = curl_file_create($file);

        $defaultOptions = array(
            'dirname' => '', //TODO :: santilize dirname to prevent hack
            'overwrite' => 0,
        );
        $options = array_replace_recursive($defaultOptions, $options);

        $data = array(
            'api_key'		=> $post_api_key,
            'iv'			=> $iv,
            'tag'			=> $tag,
            'file_image'	=> $cFile,
            'file_name' 	=> $file_name,
            'dimensions' 	=> json_encode($dimensions),
            'options'       => json_encode($options),
        );

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl) ;

        $jsonResult = json_decode($result);
        curl_close($curl);

        if($jsonResult == null){
            return array('error' => 1, 'message' => $result);
        }
        else return (array)$jsonResult;
    }
}