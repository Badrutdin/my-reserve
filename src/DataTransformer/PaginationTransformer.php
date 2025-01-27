<?php

namespace App\DataTransformer;

use App\DTO\PaginationDTO;

class PaginationTransformer
{
    public function transform($page,$limit,$total,$pages): PaginationDTO
    {
        return new PaginationDTO(
            page: $page,
            limit: $limit,
            total: $total,
            pages: $pages
        );
    }
}