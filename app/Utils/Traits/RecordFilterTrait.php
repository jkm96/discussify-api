<?php

namespace App\Utils\Traits;

trait RecordFilterTrait
{
    use DateFilterTrait;

    /**
     * @param $query
     * @param $params
     * @return void
     */
    private function applyPostFilters($query, $params)
    {
        $this->applyDateFilters($query, $params['period_from'] ?? null, $params['period_to'] ?? null);
        $this->applyPostSearchTermFilter($query, $params['search_term'] ?? null);
    }

    /**
     * @param $query
     * @param $searchTerm
     * @return void
     */
    private function applyPostSearchTermFilter($query, $searchTerm)
    {
        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('slug', 'like', '%' . $searchTerm . '%')
                    ->orWhere('tags', 'like', '%' . $searchTerm . '%');
            });
        }
    }
}
