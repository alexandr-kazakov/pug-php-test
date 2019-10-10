<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_WARNING);
//error_reporting(E_ALL | E_ERROR | E_WARNING | E_PARSE);

/*
 * Для корректности кофигурационного файла можно использовать https://jsonformatter.curiousconcept.com/
 */
class Config
{
    private $recipient;
    private $config_file;
    private $config;
    public function __construct()
    {
        $this->config_file = 'config.json';
        $this->config = $this->config_read($this->config_file);
        $this->recipient = $this->config['recipient'];
    }
    private function config_read($config_file)
    {
        $string = file_get_contents($config_file);//"/home/michael/test.json"
        if ($string === false) {
            new Exception('error during parse json file');
        }

        $json_a = json_decode($string, true);
        if ($json_a === null) {
            new Exception('error during parse json file');
        }
        return $json_a;
    }
    public function getRecipient(){
        return $this->recipient;
    }
    public function getAllowedFiles(){
        return $this->config['allowedFiles'];
    }
    public function getCommonUploadDir(){
        return $this->config['commonUploadDir'];
    }
    public function getMaxFileSize(){
        return $this->config['maxFileSize'];
    }
}

function dump($arr)
{
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

function dd($arr)
{
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
    die();
}
function removeDirectory($dir)
{
    if ($objs = glob($dir . "/*")) {
        foreach ($objs as $obj) {
            is_dir($obj) ? removeDirectory($obj) : unlink($obj);
        }
    }
    @rmdir($dir);
}
//$msg = print_r($_REQUEST, 1) . PHP_EOL;
//$msg .= 'REQUEST_METHOD - ' . $_SERVER['REQUEST_METHOD'] . PHP_EOL . PHP_EOL;
//file_put_contents('log.txt', date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
//file_put_contents('log.txt', $msg, FILE_APPEND);