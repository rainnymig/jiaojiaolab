<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\File;
use Sunra\PhpSimple\HtmlDomParser;
use App\Steamuser;

use Illuminate\Http\Request;

class FileUploadController extends Controller {

	//
    
    protected $fileService;

    public function __construct(FileUploadService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function uploadBgImage($steamid, Request $request)
    {

        if(isset($_FILES['bgImageFileUpload']))
        {
            $file = $_FILES['bgImageFileUpload']; 
            $filetype = strtolower(strstr($file['name'], '.'));
            if($filetype != '.jpg' && $filetype != '.jpeg' && $filetype != '.png' && $filetype != '.bmp' && $filetype != '.gif')
            {
                return 'wrongfiletype';
            }
            $filename = $file['name'];
            $folder = 'backgroundImage/';
            $path = $folder.$filename;
            $content = File::get($file['tmp_name']);

            $result = $this->fileService->saveFile($path, $content);

            if($result == false)
            {
                return 'uploadfailure';
            } 
            else
            {
                return $result;
            }
        }
        else
        {
            return 'nofileselect';
        }
    }

    public function setBgImage($steamid, Request $request)
    {
        $imageurl = $request->get('imageUrl');
        if($imageurl == '')
        {
            return '';
        }
        if($imageurl == 'useSteamBg')
        {
            $requestUrl = 'http://steamcommunity.com/profiles/'.$steamid;
            $result = file_get_contents($requestUrl);
            $playBackgroundHtml = HtmlDomParser::str_get_html($result);
            $rawData = $playBackgroundHtml->find('.no_header');
            if($rawData[0]->style != '')
            {
                $steamBackground = $rawData[0]->style;
                $steamBackground = strstr($steamBackground, 'h');
                $steamBackground = strstr($steamBackground, '\'', true);
            }
            else
            {
                $steamBackground = '';
            }
            $queryResult = Steamuser::where('steamid', $steamid)->first();
            $queryResult->backgroundImage = $steamBackground;
            $queryResult->save();
            return $steamBackground;
        }
        else
        {
            $queryResult = Steamuser::where('steamid', $steamid)->first();
            $queryResult->backgroundImage = $imageurl;
            $queryResult->save();
            return $imageurl;
        }
    }

}
