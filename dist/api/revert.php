<? //Revert Это у нас DELETE метод
//filePond нам вернул UniqFileId для того чтобы мы могли по нему определить файл для удаления.
// см https://pqina.nl/filepond/docs/patterns/api/server/
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Origin: *");

require('config.php');
$config = new Config();
$common_upload_dir = $config->getCommonUploadDir();

$uniq_filePond_FileId = trim(file_get_contents('php://input'));
if (!empty($uniq_filePond_FileId)) {
    $common_upload_dir = "uploads";
    $dirToDel = $common_upload_dir . '/' . $uniq_filePond_FileId;
    if (file_exists($dirToDel)) {
        removeDirectory($dirToDel);
    }
}