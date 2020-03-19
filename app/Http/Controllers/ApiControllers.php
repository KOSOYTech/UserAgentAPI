<?php

namespace App\Http\Controllers;

abstract class ApiControllers
{

protected $model;

public function get(Request $request ){

    $limit = (int) $request->get('limit', 100);
    $offset = (int) $request->get('offset', 0);

    $result = $this->model->limit($limit)->offset($offset)->get();

    $this->sendResponse($result, 'OK', 200);
}

public function detail(string $objectName = null){

}

}

?>