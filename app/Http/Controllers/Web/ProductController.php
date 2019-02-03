<?php

namespace App\Http\Controllers\Web;

use App\Models\Category;
use App\Models\Config;
use App\Models\Guid;
use App\Models\Join;
use App\Models\News;
use App\Models\Pic;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $category = Category::get();
        $data = [];
        foreach ($category as $value) {
            $products = Product::where('status', 1)->where('category_id', $value['id'])->get();

            $p = [];
            foreach ($products as $product) {
                $imgs = [];
                foreach ($product->img as $img) {
                    $imgs[] = env('APP_URL') . $img;
                }
                $p[] = $imgs;
            }
            $data[] = [
                'category' => $value['name'],
                'product' => $p
            ];
        }
        return $this->success($data);
    }

    public function config()
    {
        $config = Config::first();
        $config->video = env('APP_URL') . $config->video;
        $config->weixin = env('APP_URL') . $config->weixin;
        return $this->success($config);
    }

    public function news()
    {
        $news = News::select('title', 'text')->orderBy('id', 'desc')->get();
        return $this->success($news);
    }

    public function guid()
    {
        $guid = Guid::select('name')->get();
        return $this->success($guid);
    }

    public function join()
    {
        $join = Join::select('title', 'content')->get();
        return $this->success($join);
    }
}
