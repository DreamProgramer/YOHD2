<?php
require_once 'class.image.php';

//WebAPIのURLを読み込む
$api_path = json_decode(file_get_contents('api_path.json'), true);
$WEBAPI_SERVER = $api_path["server"].$api_path["php"];

//ローカルファイルのパス
$LOCAL_FILE_PATH = dirname(__FILE__).'/';
//ブラウザからアップロードされた画像を配置しておくディレクトリのパス
$UPLOAD_IMG_PATH = "image/";
$FILE_NAME = "sample.jpg";

$postfields = array();
$four_img = new Image($UPLOAD_IMG_PATH.$FILE_NAME);
$four_img->name($FILE_NAME);
$four_img_info = getimagesize($UPLOAD_IMG_PATH.$FILE_NAME);
$four_img->width($four_img_info[0]/2);
$four_img->height($four_img_info[1]/2);
if (isset($_POST['camera'])) {
  switch($_POST['camera']) {
    case 0:
      $four_img->crop(0, 0);
      break;
    case 1:
      $four_img->crop($four_img_info[0]/2, 0);
      break;
    case 2:
      $four_img->crop(0, $four_img_info[1]/2);
      break;
    case 3:
      $four_img->crop($four_img_info[0]/2, $four_img_info[1]/2);
      break;
  }
}
$four_img->save();
$postfields['api_file'] = '@'.$LOCAL_FILE_PATH.$UPLOAD_IMG_PATH.$FILE_NAME;

//$_POSTの内容をpostfieldsに追加します
foreach (array_keys($_POST) as $key) {
  $postfields[$key] = $_POST[$key];
}    

//libcurlを用いてWebAPIサーバと通信します
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $WEBAPI_SERVER);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

/* WebAPIサーバから返されたJSONをdecodeし、
ローカルWebサーバ上にアップロードされた画像のパスを追加した後、再度JSONにencodeする */
$res = json_decode(curl_exec($ch), true);

if(!isset($res))
{
  $res["status"] = "ERROR";
  $res["result"] = "error in connect with ".$WEBAPI_SERVER;
}

$res["image_file_name"] = basename($FILE_NAME);


/* ブラウザ側でINPUTとOUTPUTを対比できるようにするため、
ブラウザからのPOSTフィールドもJSONに加えて投げ戻す */
if(isset($postfields)) {
  $res["input_fields"] = $postfields;
}

//JSONで結果をブラウザに返します
echo json_encode($res);

curl_close($ch);

?>