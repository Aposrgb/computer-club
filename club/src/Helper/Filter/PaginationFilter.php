<?php
namespace App\Helper\Filter;

use Symfony\Component\Validator\Constraints as Assert;
use App\Service\ValidatorService;

class PaginationFilter
{
    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['pagination'])]
    protected $page;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['pagination'])]
    protected $limit;

    public function getPage()
    {
        return $this->page;
    }

    public function setPage($page): self
    {
        $this->page = $page;
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit): self
    {
        $this->limit = $limit;
        return $this;
    }
}