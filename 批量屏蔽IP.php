<?php
//宝塔批量屏蔽IP
set_time_limit(0);
ini_set('memory_limit','2048M');
session_start();
$_SESSION['kk']=0;
class bt_api {
  private $BT_KEY = "xxxxxxxxxxx";  //密钥
  private $BT_PANEL = "http://127.0.0.1:8888";     //地址

  public function __construct($bt_panel = null,$bt_key = null){
    if($bt_panel) $this->BT_PANEL = $bt_panel;
    if($bt_key) $this->BT_KEY = $bt_key;
  }
  public function banIP($txt){
    $urlreal = $this->BT_PANEL.'/firewall?action=AddDropAddress';
    $p_data = $this->GetKeyData();
    $file = fopen($txt, "r");
    $urls=array();
    $i=0;
    while(!feof($file)){$urls[$i]= fgets($file);$i++;}
    fclose($file);
    $url=array_filter($urls);
    foreach ($url as $k => $v) {
        $p_data['port'] = preg_replace("/\s|　/","",$v);
        $p_data['type'] = 'address';
        $p_data['ps'] = '恶意IP';
        $result = $this->HttpPostCookie($urlreal,$p_data);
        $data = json_decode($result,true);
        var_dump($data);
    }
  }
    private function GetKeyData(){
      $now_time = time();
      $p_data = array(
      'request_token' =>  md5($now_time.''.md5($this->BT_KEY)),
      'request_time'  =>  $now_time
    );
      return $p_data;    
    }
    private function HttpPostCookie($url, $data,$timeout = 60)
    {
        $cookie_file='./'.md5($this->BT_PANEL).'.cookie';
        if(!file_exists($cookie_file)){
            $fp = fopen($cookie_file,'w+');
            fclose($fp);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}


$api = new bt_api();
$r_data = $api->banIP("blackip.txt");
echo json_encode($r_data);

?>