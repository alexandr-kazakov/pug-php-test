<? //Submit Это у наc POST метод
// header("Access-Control-Allow-Origin: *");
require('config.php');

$config = new Config();
$fileTypes = $config->getAllowedFiles();//список разрешенных расширений файлов
$recipient = $config->getRecipient();
$common_upload_dir = $config->getCommonUploadDir();
$maxFileSize = $config->getMaxFileSize();
$uploaded_file_dirs = $_POST['fileToUpload'];//filePond нам вернул UniqFileId вместо файла. см https://pqina.nl/filepond/docs/patterns/api/server/
$files_to_email = [];

$tel = trim($_POST["phone"]);
$email = trim($_POST["email"]);
$message = trim($_POST["message"]);


$subject = "Письмо с сайта бумажные-стаканы.москва, от посетителя: $email";

$email_content = "Телефон отправителя: $tel\n\n";
$email_content .= "Email отправителя: $email\n\n";
$email_content .= "Сообщение отправителя: \n$message\n";

$email_headers = "< noreply@xn----7sbbabzg9ammf8bng8ji.xn--80adxhks >";

if (!empty($_POST['subject'])) {
    http_response_code(400);
    exit;
}

if (empty($uploaded_file_dirs)){
    send_mail($recipient, $email_headers, $subject, $email_content, $files_to_email, 0);
} else {
    foreach ($uploaded_file_dirs as $key => $dir) {
        $target_file_dir = $common_upload_dir . '/' . $dir;
        $files_to_email[$key]['folder'] = $target_file_dir;
        $files_to_email[$key]['name'] = getSimpleFilesList($target_file_dir)[0];
    }
    /*
     * Пример итогового массива:
    [
        [0] => Array
            (
                [folder] => uploads/2019-09-09_20-08-08-024100
                [name] => 123sfdsdfTESTTERST.png
            )

        [1] => Array
            (
                [folder] => uploads/2019-09-09_20-07-44-916500
                [name] => russkoe slovo.png
            )
    ]
     */
    foreach ($files_to_email as $file) {
        $folder = $file['folder'];
        $file = $folder . '/' . $file['name'];
        $result = fileValidate($file, $fileTypes, $maxFileSize);
        if ($result) {
            echo 'Ошибка с файлом: ' . $file . PHP_EOL;
            removeDirectory($folder);
            echo $result;
            die();
        }
    }

    send_mail($recipient, $email_headers, $subject, $email_content, $files_to_email, 0);
}

function send_mail($to, $from, $subj, $text, $files_to_email, $isHTML = false)
{
    $boundary = strtoupper(md5(uniqid(rand())));
    $headers = "From: " . $from . "\nContent-Type: multipart/mixed;boundary=\"$boundary\"\n";
    if (!$isHTML) {
        $type = 'text/plain';
    } else {
        $type = 'text/html';

    }
    //  echo "BODY: :".$text;
    $body = '--' . $boundary . "\nContent-Type: " . $type . "; charset=utf-8\nContent-Transfer-Encoding: Quot-Printed\n\n" . $text . "\n\n";

    foreach ($files_to_email as $file) {
        $filename = $file['name'];
        $pathToFile = $file['folder'];
        $file = fopen($pathToFile . '/' . $filename, "rb");
        $filecontent = fread($file, filesize($pathToFile . '/' . $filename));
        fclose($file);
        if (strlen($filename) > 0 && $filecontent)
            $body .= '--' . $boundary . "\nContent-Type: application/octet-stream; file_name = \"" . $filename . "\"\nContent-Transfer-Encoding: base64\nContent-Disposition: attachment; filename = \"" . $filename . "\"\n\n" . chunk_split(base64_encode($filecontent)) . "\n";
        //echo "\nBODY: " . $body;
    }

    if (mail($to, $subj, $body, $headers)) {
        removeAllSendedFiles($files_to_email);
        http_response_code(200);
        echo "Спасибо! Ваше сообщение было отправлено.";

    } else {
        http_response_code(500);
        echo "К сожалению! Что-то пошло не так, и мы не смогли отправить ваше сообщение.";
    }
}

function removeAllSendedFiles($files_to_email)
{
    foreach ($files_to_email as $file) {
        $pathToFile = $file['folder'];
        removeDirectory($pathToFile);
    }
}
function fileValidate($file, $fileTypes, $maxFileSize)
{
    $result=array();

    // Check fiLe size
    if (filesize($file) > $maxFileSize) {
        $msg = "Файл больше допустимого размера" . PHP_EOL;
        $result['messages'] =  $msg;
        $result['code'] = 500;
        die(json_encode($result));
    }
    $loadedFileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
// Allow certain fiLe formats
    if (!in_array($loadedFileType, $fileTypes)) {
        $msg = "Допустимые типы файлов: " . implode(', ', $fileTypes) . PHP_EOL;
        $msg .= "А у вашего файла - " . $loadedFileType . PHP_EOL;
        $result['message'] =  $msg;
        $result['code'] = 500;
        die(json_encode($result));
    }
    return false;
}
/**
 * Вернёт массив, содержащий имена файлов из указанной директории
 * (содержащиеся директории будут проигнорированы)
 *
 * @param string $dirpath - путь к диретории
 * @return array  - массив имён файлов
 */
function getSimpleFilesList($dirpath)
{
    $result = [];
    $cdir = scandir($dirpath);
    foreach ($cdir as $value) {
        // если это "не точки" и не директория
        if (!in_array($value, array(".", ".."))
            && !is_dir($dirpath . DIRECTORY_SEPARATOR . $value)) {

            $result[] = $value;
        }
    }
    return $result;
}

