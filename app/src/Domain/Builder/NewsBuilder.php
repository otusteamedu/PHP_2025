<?php

declare(strict_types=1);

namespace App\Domain\Builder;

use App\Domain\Entity\Category;
use App\Domain\Entity\News;
use App\Domain\ValueObject\Author;
use App\Domain\ValueObject\Content;
use App\Domain\ValueObject\Title;

class NewsBuilder
{
    private Title $title;
    private Author $author;
    private Category $category;
    private Content $content;

    public function build(): News
    {
        return new News($this);
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function setTitle(string $title): NewsBuilder
    {
        $this->title = new Title($title);
        return $this;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function setAuthor(string $author): NewsBuilder
    {
        $this->author = new Author($author);
        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): NewsBuilder
    {
        $this->category = $category;
        return $this;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(string $content): NewsBuilder
    {
        $this->content = new Content($content);
        return $this;
    }
}
