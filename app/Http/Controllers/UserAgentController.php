<?php

namespace App\Http\Controllers;

class UserAgentController extends ApiControllers
{

    public function __construct(UserAgent $model)
    {
        $this->model = $model;
    }

}

?>