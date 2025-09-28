<?php


use Core\Response;
use Core\Router;

function d($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}
function dd($value)
{
    d($value);
    die();
}

function authorize($condition, $statusCode = Response::FORBIDDEN)
{
    if (!$condition) {
        Router::push($statusCode);
        exit;
    }
}

function requireFromBase($path, $variables = [])
{
    $fullPath = BASE_PATH . $path;
    if (file_exists($fullPath)) {
        extract($variables);
        return require $fullPath;
    } else {
        throw new Exception("File not found: {$fullPath}");
    }
}

function requireFromView($viewPath, $variables = [])
{
    $fullPath = BASE_PATH . 'views/' . $viewPath;
    if (file_exists($fullPath)) {
        // Extract variables to make them available in the view
        extract($variables);
        return require $fullPath;
    } else {
        throw new Exception("View file not found: {$fullPath}");
    }
}

function setupClassAutoLoader()
{
    // PHP 內建的自動載入機制，當寫 new Database() 但沒有 require 'core/Database.php' 時
    // 這個 function 就會被觸發，$class 參數會是使用到的 class 名稱，例如 Database
    // 我們就能根據這個名稱來動態載入對應的檔案
    spl_autoload_register(function ($class) {
        // dd($class); // 可以用來查看載入的 class 名稱

        try {
            // 處理有 namespace 的 class
            if (strpos($class, '\\') !== false) {
                // 將 namespace 轉換為檔案路徑，使用 DIRECTORY_SEPARATOR 確保跨平台相容性
                $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class);
                $filePath = "{$classPath}.php";
            } else {
                // 處理沒有 namespace 的 class（向後相容性）
                $filePath = "core" . DIRECTORY_SEPARATOR . "{$class}.php";
            }

            $fullPath = BASE_PATH . $filePath;

            // 在載入前檢查檔案是否存在
            if (file_exists($fullPath)) {
                require $fullPath;
                return true;
            } else {
                dd("spl_autoload_register not found '{$class}'");
                return false;
            }

            return false;
        } catch (Exception $e) {
            // 記錄錯誤到日誌，但不暴露詳細資訊
            error_log("spl_autoload_register not found '{$class}': " . $e->getMessage());
            return false;
        }
    });
}
