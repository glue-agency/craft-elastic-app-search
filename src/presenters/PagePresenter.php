<?php

namespace GlueAgency\ElasticAppSearch\presenters;

class PagePresenter extends BasePresenter
{

    public int $current;

    public int $total_pages;

    public int $total_results;

    public int $size;

    public function hasNext() : bool
    {
        return $this->current < $this->total_pages;
    }

    public function getNextPage(): ?int
    {
        return $this->hasNext() ? $this->current + 1 : null;
    }

    public function getNextPages(int $count): array
    {
        $next = [];

        for($i = 1; $i <= $count; $i++) {
            if(($this->current + $i) < $this->total_pages) {
                $next[] = $this->current + $i;
            }
        }

        return $next;
    }

    public function hasPrev(): bool
    {
        return $this->current > 1;
    }

    public function getPrevPage(): ?int
    {
        return $this->hasPrev() ? $this->current - 1 : null;
    }

    public function getPrevPages(int $count): array
    {
        $prev = [];

        for($i = 1; $i <= $count; $i++) {
            if(($this->current - $i) > 0) {
                $prev[] = $this->current - $i;
            }
        }

        return array_reverse($prev);
    }
}
