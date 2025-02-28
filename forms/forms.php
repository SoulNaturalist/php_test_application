<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function readSheetData($filePath, $sheetName) {
    if (!file_exists($filePath)) {
        die("Файл не найден: $filePath");
    }

    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getSheetByName($sheetName);

    if (!$sheet) {
        die("Лист '$sheetName' не найден в файле.");
    }

    $data = $sheet->toArray();
    $data = array_filter(array_map('array_filter', $data));
    $data = array_merge(...$data);

    return $data;
}

$sheet1Data = [];
$sheet2Data = [];
$form1_result = '';
$form2_result = '';

if (isset($_POST['check_form1']) || isset($_POST['check_form2'])) {
    $excelFilePath = '1.xlsx';

    $sheet1Data = readSheetData($excelFilePath, 'Список 1'); 
    $sheet2Data = readSheetData($excelFilePath, 'Список 2'); 

    if (isset($_POST['check_form1'])) {
        $form1_value = isset($_POST['form1_value']) ? trim($_POST['form1_value']) : '';

        if (in_array($form1_value, $sheet2Data)) {
            $form1_result = "Значение '$form1_value' найдено в Списке 2.";
        } else {
            $form1_result = "Значение '$form1_value' отсутствует в Списке 2.";
        }
    }

    if (isset($_POST['check_form2'])) {
        $form2_value = isset($_POST['form2_value']) ? trim($_POST['form2_value']) : '';

        if (in_array($form2_value, $sheet1Data)) {
            $form2_result = "Значение '$form2_value' найдено в Списке 1.";
        } else {
            $form2_result = "Значение '$form2_value' отсутствует в Списке 1.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск значений в списках</title>
</head>
<body>

<h2>Поиск значений в списках</h2>


<form method="post" action="">
    <h3>Форма 1</h3>
    <input type="text" name="form1_value" placeholder="Введите значение для поиска" value="<?php echo isset($_POST['form1_value']) ? htmlspecialchars($_POST['form1_value']) : ''; ?>">
    <br>
    <input type="submit" name="check_form1" value="Проверить">
</form>

<?php
if (isset($_POST['check_form1'])) {
    echo "<p>$form1_result</p>";
}
?>


<form method="post" action="">
    <h3>Форма 2</h3>
    <input type="text" name="form2_value" placeholder="Введите значение для поиска" value="<?php echo isset($_POST['form2_value']) ? htmlspecialchars($_POST['form2_value']) : ''; ?>">
    <br>
    <input type="submit" name="check_form2" value="Проверить">
</form>

<?php
if (isset($_POST['check_form2'])) {
    echo "<p>$form2_result</p>";
}
?>

</body>
</html>