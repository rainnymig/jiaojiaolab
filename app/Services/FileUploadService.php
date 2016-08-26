<?php namespace App\Services;

use Carbon\Carbon;
use Dflydev\ApacheMimeTypes\PhpRepository;
use Illuminate\Support\Facades\Storage;

class FileUploadService {
    protected $disk;
    protected $mimeDetect;

    public function __construct(PhpRepository $mimeDetect)
    {
        $this->disk = Storage::disk(config('lab.uploads.storage'));
        $this->mimeDetect = $mimeDetect;
    }

    //  返回完整网络路径
    public function fileWebpath($path)
    {
        $path = rtrim(config('lab.uploads.webpath'), '/') . '/' .ltrim($path, '/');
        return url($path);
    }

    //  返回文件 MIME 类型
    public function fileMimeType($path)
    {
        return $this->mimeDetect->findType(pathinfo($path, PATHINFO_EXTENSION));
    }

    //  判断文件是否图片
    public function isImage($mimeType)
    {
        return starts_with($mimeType, 'image/');
    }

    //  清洁路径
    protected function cleanFolder($folder)
    {
        return '/' . trim(str_replace('..', '', $folder), '/');
    }

    public function saveFile($path, $content)
    {
        $path = $this->cleanFolder($path);
        if($this->disk->exists($path))
        {
            $this->disk->delete($path);
        }
        $result = $this->disk->put($path, $content);
        if($result == false)
        {
            return false;
        }
        else
        {
            return $this->fileWebpath($path);
        }
    }
}
