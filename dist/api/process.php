<? //process Это у наc POST метод
//Сгенерим уникальный fileID и отдадим его filePond'у

header("Access-Control-Allow-Origin: *");
require('config.php');
$config = new Config();
$common_upload_dir = $config->getCommonUploadDir();

$now = DateTime::createFromFormat('U.u', microtime(true));
$uniq_filePond_FileId = $now->format("Y-m-d_H-i-s-u");

if (!file_exists($uniq_filePond_FileId)) {
    mkdir($common_upload_dir . '/' . $uniq_filePond_FileId, 0777, true);
}

$fileName = $_FILES["fileToUpload"]['name'][0];
//$convertedText = mb_convert_encoding($convertedText, 'windows-1251', mb_detect_encoding($convertedText));
$fileName = transliterate($fileName);
$fileName = mb_convert_encoding($fileName, 'windows-1251', mb_detect_encoding($fileName));
$target_file = $common_upload_dir . '/' . $uniq_filePond_FileId . '/' . $fileName;

if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][0], $target_file)) {
    echo $uniq_filePond_FileId; // 3. server returns unique location id 12345 in text/plain response
} else {
    //echo "Sorry, there was an error uploading your file.";
}

// Транслитерация строк.
function transliterate($str)
{
    $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
    return str_replace($rus, $lat, $str);
}
