<?php

namespace Domain\Entity;

use \Domain\ValueObject\Url;

class News  
{  
    private ?int $id = null; 
    private ?string $date = null; 
    private ?string $title = null;  
  
    public function __construct(  
        private Url $url
    )  
    {  
    }  

    public function getId(): ?int  
    {  
        return $this->id;  
    }  
  
    public function getUrl(): Url  
    {  
        return $this->url;  
    }  

    public function getTitle(): ?string
    {  
        //$str = file_get_contents(($this->url)->getValue());
        $doc = new \DOMDocument();
        @$doc->loadHTML(@file_get_contents(($this->url)->getValue()));
        $titlelist = $doc->getElementsByTagName("title");
        if($titlelist->length > 0){
            $this->title = $titlelist->item(0)->nodeValue;
        }
        return $this->title;  
    }  

    public function getDate(): ?string
    {  
        $this->date = date("Y.m.d H:i:s");
        return $this->date;  
    } 
}