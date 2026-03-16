<?php

namespace App\Services\Share;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * 禁止上傳的副檔名
     */
    const FORBIDDEN_EXTENSIONS = [
        // 可執行檔
        'exe', 'com', 'bat', 'cmd', 'sh', 'bash', 'zsh',
        // 腳本類
        'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
        'asp', 'aspx', 'jsp', 'jspx',
        'py', 'rb', 'pl', 'cgi',
        // 系統類
        'dll', 'so', 'dylib',
        'msi', 'deb', 'rpm', 'dmg', 'pkg',
        // 設定類
        'htaccess', 'htpasswd', 'env',
    ];

    /**
     * 禁止的 MIME Type
     */
    const FORBIDDEN_MIME_TYPES = [
        'application/x-executable',
        'application/x-msdownload',
        'application/x-sh',
        'application/x-php',
        'text/x-php',
        'application/x-httpd-php',
    ];

    /**
     * 上傳檔案
     *
     * @param UploadedFile $file  上傳的檔案
     * @param string       $type  允許類型（image / document / video）
     * @param string       $disk  儲存磁碟
     *
     * @return string 儲存路徑
     * @throws \Exception
     */
    public function upload(UploadedFile $file, string $type = 'image', string $disk = 'public'): string
    {
        # 驗證是否為危險類型
        $this->checkForbidden($file);

        # 驗證真實 MIME Type（magic bytes）
        $this->checkRealMimeType($file, $type);

        # 驗證檔案大小
        $this->checkFileSize($file, $type);

        # 重新命名（UUID + 原始副檔名）
        $extension = strtolower($file->getClientOriginalExtension());
        $filename  = Str::uuid() . '.' . $extension;

        # 儲存到指定磁碟，路徑加年月分類
        $directory = 'uploads/' . $type . '/' . now()->format('Y/m');
        $path = $file->storeAs($directory, $filename, $disk);

        return $path;
    }

    /**
     * 刪除檔案
     *
     * @param string $path 檔案路徑
     * @param string $disk 儲存磁碟
     */
    public function delete(string $path, string $disk = 'public'): void
    {
        if (!empty($path) && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * 檢查禁止上傳的類型
     *
     * @throws \Exception
     */
    private function checkForbidden(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, self::FORBIDDEN_EXTENSIONS, true)) {
            throw new \Exception("禁止上傳此類型檔案：{$extension}");
        }

        # 防止雙重副檔名攻擊（如 shell.php.jpg）
        $originalName = $file->getClientOriginalName();
        $parts = explode('.', $originalName);
        if (count($parts) > 2) {
            foreach (array_slice($parts, 1, -1) as $part) {
                if (in_array(strtolower($part), self::FORBIDDEN_EXTENSIONS, true)) {
                    throw new \Exception("檔名包含危險副檔名：{$originalName}");
                }
            }
        }
    }

    /**
     * 驗證真實 MIME Type（讀取 magic bytes）
     *
     * @throws \Exception
     */
    private function checkRealMimeType(UploadedFile $file, string $type): void
    {
        $config = config("upload.{$type}");
        if (empty($config)) {
            throw new \Exception("未定義的上傳類型：{$type}");
        }

        $realMimeType = mime_content_type($file->getRealPath());

        # 禁止的 MIME Type
        if (in_array($realMimeType, self::FORBIDDEN_MIME_TYPES, true)) {
            throw new \Exception('偵測到危險的檔案內容類型');
        }

        # 不在允許清單內
        if (!in_array($realMimeType, $config['mime_types'], true)) {
            throw new \Exception("不支援的檔案內容類型：{$realMimeType}");
        }
    }

    /**
     * 驗證檔案大小
     *
     * @throws \Exception
     */
    private function checkFileSize(UploadedFile $file, string $type): void
    {
        $config = config("upload.{$type}");
        $maxSizeKB = $config['max_size'] ?? 5120;
        $fileSizeKB = $file->getSize() / 1024;

        if ($fileSizeKB > $maxSizeKB) {
            $maxSizeMB = round($maxSizeKB / 1024);
            throw new \Exception("檔案大小超過限制（上限 {$maxSizeMB}MB）");
        }
    }
}
