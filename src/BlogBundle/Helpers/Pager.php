<?php

namespace BlogBundle\Helpers;

class Pager
{
    protected $page;
    protected $perPage;
    protected $count;
    protected $maxButtonCount;
    protected $pageCount;

    public function __construct($page, $perPage, $count, $maxButtonCount=5)
    {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->count = $count;
        $this->maxButtonCount = $maxButtonCount;
        $this->pageCount = ceil($this->count / $this->perPage);
    }

    public function getPageCount()
    {
        return $this->pageCount;
    }

    public function getChunk() {
        if ($this->pageCount <= $this->maxButtonCount) {
            return range(1, $this->pageCount);
        }

        $offset = $this->page;

        //С середины или конца
        if (($this->maxButtonCount % 2) == 0) {
            $left = ceil(($this->maxButtonCount - 1) / 2);
            $right = $this->maxButtonCount - 1 - $left;
        } else {
            $left = $right = ($this->maxButtonCount - 1) / 2;
        }

        //В начале списка
        if ($left >= $offset) {
            while ($left >= $offset) {
                $offset++;
            }
        }
        //В конце списка
        elseif ($right > $this->pageCount - $offset) {
            while ($right > $this->pageCount - $offset) {
                $offset--;
            }
        }

        return  range($offset - $left, $offset + $right);
    }


}