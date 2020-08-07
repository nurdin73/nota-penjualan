<?php
namespace App\Http\Helpers;

class Functions
{
    public static function ArrayDuplicateRemove($array, $keep_key = false)
    {
        $duplicate_keys = [];
        $temp = [];
        foreach ($array as $key => $value) {
            if(is_object($value))
                $value = (array)$value;
            
            if(!in_array($value, $temp))
                $temp[] = $value;
            else 
                $duplicate_keys[] = $key;
        }

        foreach($duplicate_keys as $key)
            unset($array[$key]);
        
        return $keep_key ? $array : array_values($array);
    }

    public static function message($text, $status)
    {
        return [
            'message' => $text,
            'status' => $status
        ];
    }

}
