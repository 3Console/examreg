<?php

namespace App;

use Carbon\Carbon;
use App\Consts;
use Auth;
use DB;
use App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Utils
{
    public static function getContentCsvFile($fileUpload, $header = [])
    {
        $content = [];

        $path = $fileUpload->getRealPath();
        $file = fopen($path, 'r');

        $columns = static::removeSpecialChars(fgetcsv($file));

        if (!empty($header) && join(Consts::CHAR_COMMA, $columns) !== join(Consts::CHAR_COMMA, $header)) {
            throw new \Exception('The header file upload is invalid.');
        }

        while (!feof($file)) {
            $value = fgetcsv($file);
            if (!empty($value) && $value !== array(null)) {
                $value = static::removeSpecialChars($value);
                $content[] = array_combine($columns, $value);
            }
        }
        fclose($file);

        return $content;
    }

    private static function removeSpecialChars(array $data)
    {
        return collect($data)->map(function ($item) {
            $item = preg_replace(Consts::REGEX_REMOVE_SPECIAL_CHAR, Consts::STRING_EMPTY, $item);
            return trim($item);
        })
        ->toArray();
    }
}
