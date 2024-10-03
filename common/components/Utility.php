<?php 

namespace common\components;

class Utility
{
    /**
     * Метод для определения пагинации kartik/GridView
     * либо убирает пагинацию либо выставляет указанный размер в $page_size
     * @param mixed $request_params request
     * @param int $page_size Размер по умолчанию
     * @return int    pagination config
     */
    public static function getPagination($request_params, $page_size){
        $param_val = 'page';
        foreach($request_params as $key => $value){
            if (strpos($key, '_tog') !== false) {
                $param_val = $value;
            }
        }
        if($param_val == 'all'){
            return 0;
        }
        return $page_size;
    }
}
