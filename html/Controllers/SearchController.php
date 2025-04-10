<?php

namespace App\Controllers;

use App\Services\ElasticsearchService;

class SearchController
{
  private $searchService;

  public function __construct()
  {
    $this->searchService = new ElasticsearchService();
  }

  public function showSearchForm()
  {
    require __DIR__.'/../views/search_form.php';
  }

  public function handleSearch()
  {
    $query = $_GET['query'] ?? '';
    $category = $_GET['category'] ?? '';
    $maxPrice = $_GET['max-price'] ?? '';
    $inStock = isset($_GET['in-stock']);

    try {
      $hits = $this->searchService->searchBooks($query, $category, $maxPrice, $inStock);

      require __DIR__.'/../views/search_results.php';
    } catch (\Exception $e) {
      echo '<p style="color:red;">Search error: ' . $e->getMessage() . '</p>';
    }
  }
}