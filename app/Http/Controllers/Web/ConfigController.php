<?php

namespace App\Http\Controllers\Web;

use App\Models\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    public function index()
    {
        $config = Config::get();
        $data = [];
        foreach ($config as $key => $value) {
            $data[$key] = $value;
        }
        return $this->success($data);
    }
}
