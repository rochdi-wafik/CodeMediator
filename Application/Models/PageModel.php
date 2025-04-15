<?php
use Core\Classes\Model;

class PageModel extends Model{
    private int $id;
    private string $slug;
    private string $title;
    private string $content;
    private int $createdAt;

    /**
     * Constructor
     */
    public function __construct(int $id, string $slug, string $title, string $content, int $createdAt){
        $this->id = $id;
        $this->slug = $slug;
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }

    /**
     * Getters
     */
    public function getId(): int{
        return $this->id;
    }
    public function getSlug(): string{
        return $this->slug;
    }
    public function getTitle(): string{
        return $this->title;
    }
    public function getContent(): string{
        return $this->content;
    }
    public function getCreatedAt(): int{
        return $this->createdAt;
    }
}
