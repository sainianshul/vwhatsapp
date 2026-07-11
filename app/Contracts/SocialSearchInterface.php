<?php

namespace App\Contracts;

interface SocialSearchInterface
{
    /**
     * Search for social media accounts by name/query.
     *
     * @param  string      $query   The search term (person name, page name, etc.)
     * @param  string|null $cookies Optional cookies for authenticated search (e.g. Facebook GraphQL)
     * @return array       Array of search result items
     */
    public function search(string $query, ?string $cookies = null): array;
    /**
     * Deep scrape a specific social media account.
     */
    public function scrapeAccount(string $url, string $cookies): array;
}
