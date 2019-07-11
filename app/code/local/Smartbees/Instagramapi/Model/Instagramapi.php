<?php 
/* We will redirect to the checkout/cart for exemple */
class Smartbees_Instagramapi_Model_Instagramapi{
    public function getImagesUrl()
    {
        $imagesURL = array();
        define( 'IG_CLIENT_ID', '6748478748f24966ab12e66a90e0c97d' );
        define( 'IG_CLIENT_SECRET', ' 47c1e2aee76044418f98e1600e3609d9' );
        define( 'IG_REDIRECT_URI', 'https://reforma.dev.wdsdev.pl' );
        define( 'IG_ACCESS_TOKEN', '16157986992.6748478.988eeeceb54942c380b20b655fea436e' );
      
        $ch = curl_init( 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . IG_ACCESS_TOKEN );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        $response_raw = curl_exec( $ch );
        $response = json_decode( $response_raw, true );
        curl_close( $ch );
        $imagesNumber = count($response['data']);
        for( $x = 0; $x < $imagesNumber; $x++ ){
            $imagesURL[]=$response['data'][$x]['images']['low_resolution']['url'];
        }
        return $imagesURL;
    }
}
?>