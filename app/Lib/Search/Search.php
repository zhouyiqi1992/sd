<?php

namespace App\Lib\Search;

class Search
{

    public $search = [];

    protected $request;

    protected $set;

    protected $deal;

    protected $searchType;

    public function __construct($request, $set, $deal = [])
    {
        $this->request = $request;
        $this->set = $set;
        $this->deal = $deal;
        $this->searchType = new SearchType();
        $this->assembleSearchKey();
    }


    protected function assembleSearchKey()
    {

        foreach ($this->set as $k) {
            if (isset($k[4])) {
                if (true === $k[4]) {
                    $val = $k[3];
                } else {
                    $val = $this->request->get($k[0]);
                    if (is_null($val)) {
                        $val = $k[3];
                    }
                }
            } else {
                $val = isset($k[3]) ? $k[3] : $this->request->get($k[0]);
            }

            if (is_null($val)) {
                continue;
            }

            if (isset($this->deal[$k[0]])) {
                $dealArr = explode('|', $this->deal[$k[0]]);
                $obj_name = 'App\Lib\Search\SearchVal\\' . ucfirst(strtolower($dealArr[0]));
                $val = (new $obj_name($val, $dealArr[1]))->getVal();
            }

            list($key, $val) = $this->searchType->setType($k[1])->setVal($val)->make();

            $filed = isset($k[2]) ? $k[2] : $k[0];
            $this->search[] = [$this->camel2Underline($filed), $key, $val];
        }
    }

    protected function camel2Underline($str)
    {
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $str));
    }

    /**
     * @return array
     */
    public function getSearch(): array
    {
        return $this->search;
    }

}
