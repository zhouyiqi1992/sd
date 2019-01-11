<?php

namespace App\Lib\Search;

class SearchType
{
    protected $type;

    protected $val;

    protected $deal = ['like', 'likeR', 'likeL'];


    public function setVal($val)
    {
        $this->val = $val;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function make()
    {
        if (in_array($this->type, $this->deal)) {
            $this->{$this->type}();
        }
        return [$this->type, $this->val];
    }

    protected function like()
    {
        $this->val = "%" . $this->val . "%";
    }

    protected function likeR()
    {
        $this->type = 'like';
        $this->val = $this->val . "%";
    }

    protected function likeL()
    {
        $this->type = 'like';
        $this->val = "%" . $this->val;
    }
}
