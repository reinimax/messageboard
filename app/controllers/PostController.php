<?php

namespace app\controllers;

use app\models\PostModel;

class PostController
{
    protected $model;

    public function __construct()
    {
        $this->model = new PostModel();
    }

    public function index()
    {
        // call model and return the retrieved data
        return $this->model->index();
    }
}
