<?php
require __DIR__ .'/TwistOAuth-master/build/TwistOAuth.phar';
$consumer_key = 'LRgNjgL1x0GFXTurnUknnvpok';
$consumer_secret = '69xTw0gkAcoBHMuWiKgh2BicQCDaiohUDu3RNTMpGO89sVTtXb';
$access_token = '1057852768059219968-AGP0aukYpypqyTwxGXhtLWpCbKRpLX';
$access_token_secret = 'klVOq3a21dtQsAUEtE3zfUfa2MOGnfc3d83FcBnM3dEtL';



$connection = new TwistOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);

$home_params = ['count' => '2'];
$home = $connection->get('statuses/home_timeline', $home_params);
foreach($home as $value){
  $text = htmlspecialchars($value->text, ENT_QUOTES, 'UTF-8', false);
  $keywords = preg_split('/,|\sOR\s/', $home_params['count']); //配列化
  foreach ($keywords as $key) {
    $text = str_ireplace($key, '<span class="keyword">'.$key.'</span>', $text);
  }
  // ツイート表示のHTML生成
  disp_tweet($value, $text);
}

function translate_k_j($text,$key,$host,$path,$params){
  if (!function_exists('com_create_guid')) {
    function com_create_guid() {
      return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
          mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
          mt_rand( 0, 0xffff ),
          mt_rand( 0, 0x0fff ) | 0x4000,
          mt_rand( 0, 0x3fff ) | 0x8000,
          mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
      );
    }
  }
  function Translate ($host, $path, $key, $params, $content) {
      $headers = "Content-type: application/json\r\n" .
          "Content-length: " . strlen($content) . "\r\n" .
          "Ocp-Apim-Subscription-Key: $key\r\n" .
          "X-ClientTraceId: " . com_create_guid() . "\r\n";
      // NOTE: Use the key 'http' even if you are making an HTTPS request. See:
      // http://php.net/manual/en/function.stream-context-create.php
      $options = array (
          'http' => array (
              'header' => $headers,
              'method' => 'POST',
              'content' => $content
          )
      );
      $context  = stream_context_create ($options);
      $result = file_get_contents ($host . $path . $params, false, $context);
      return $result;
  }
  $requestBody = array (
      array (
          'Text' => $text,
      ),
  );
  $content = json_encode($requestBody);
  $result = Translate ($host, $path, $key, $params, $content);
  // Note: We convert result, which is JSON, to and from an object so we can pretty-print it.
  // We want to avoid escaping any Unicode characters that result contains. See:
  // http://php.net/manual/en/function.json-encode.php
  $json = json_encode(json_decode($result), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  $json_b = explode('"translations":',$json);
  $json_c = explode('"text": "',$json_b[1]);
  $json_d = explode('"',$json_c[1]);
  return $json_d[0];

}

function send_mail($updated,$tweet_content,$url){
  $k_key = 'cc8b4cb6eb5e463c985e630a2b7d3bf0';
  $k_host = "https://api.cognitive.microsofttranslator.com";
  $k_path = "/translate?api-version=3.0";
  $k_params = "&to=ja";

  mb_language("Japanese");
  mb_internal_encoding("UTF-8");
  $japan_g=translate_k_j($tweet_content,$k_key,$k_host,$k_path,$k_params);
  $to = "intern2018-02@m-up.com";
  $title = "@offclASTRO UPDATE";

  $header = "from: $to<".$to. ">\n";
  $header .= "mailto: $to<".$to. ">\n";
  $header .= "Content-type: text/plain; charset=\"UTF-8\"\n";
  //  $headers = "From: {$to} <{$to}>"."\r\n";
  $content = "data:\r\n".$updated."\r\n\r\ntweet ko:\r\n".$tweet_content."\r\n\r\ntweet ja:\r\n".$japan_g."\r\n\r\nurl:\r\nhttps://".$url;
  mail($to, $title, $content,$header);
}

function disp_tweet($value, $text){
  $icon_url = $value->user->profile_image_url;
  $tweet_content = htmlspecialchars($value->text, ENT_QUOTES, 'UTF-8', false);
  $icon_url_2 = $value->user;
  $screen_name = $value->user->screen_name;
  $updated = date('Y/m/d H:i', strtotime($value->created_at));
  $tweet_id = $value->id_str;
  $url = 'https://twitter.com/' . $screen_name . '/status/' . $tweet_id;

  echo '<table border="1">';
  echo '<div class="tweetbox">' . PHP_EOL;
  echo '<tr>';
  echo '<td>';
  echo '<div class="thumb">' . '<img alt="" src="' . $icon_url . '">' . '</div>' . PHP_EOL;
  echo '<div class="meta"><a target="_blank" href="' . $url . '">' . $updated . '</a>' . '<br>@' . $screen_name .'</div>' . PHP_EOL;
  echo '</td>';
  echo '<td>';
  echo '<div class="tweet">' . $text . '</div>' . PHP_EOL;
  if(isset($value->entities->media[0]->media_url)){
    $tweet_img = $value->entities->media[0]->media_url;
    echo '<div class="tweet_img"><img src="' .$tweet_img. '"></div>' . PHP_EOL;
  }
  $tweet_content=explode("https://",$tweet_content);
  echo '</td>';
  echo '</tr>';
  echo '</div>' . PHP_EOL;
  echo '<br>';
  send_mail($updated,$tweet_content[0],$tweet_content[1]);

}
?>
