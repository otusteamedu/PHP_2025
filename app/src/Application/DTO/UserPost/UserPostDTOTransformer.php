<?php
declare(strict_types=1);


namespace App\Application\DTO\UserPost;


use App\Domain\Aggregate\User\UserPost;

class UserPostDTOTransformer
{

    public function fromEntity(UserPost $post): UserPostDTO
    {
        $postDto = new UserPostDTO();
        $postDto->id = $post->id;
        $postDto->title = $post->getTitle();
        $postDto->content = $post->getContent();

        return $postDto;
    }

    /**
     * @param array<UserPost> $posts
     *
     * @return array<UserPostDTO>
     */
    public function fromEntityList(array $posts): array
    {
        $postDTOs = [];
        foreach ($posts as $post) {
            $postDTOs[] = $this->fromEntity($post);
        }

        return $postDTOs;
    }

}