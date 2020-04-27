<?php declare(strict_types=1);

namespace App\Entity;

class MovieInfo implements \JsonSerializable
{
    private int $id;

    private string $title;

    private ?string $trailerLink;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTrailerLink(): ?string
    {
        return $this->trailerLink;
    }

    public function setTrailerLink(?string $trailerLink): void
    {
        $this->trailerLink = $trailerLink;
    }

    public function jsonSerialize()
    {
        $array = [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
        ];

        if ($this->getTrailerLink() !== null) {
            $array['trailerLink'] = $this->getTrailerLink();
        }

        return $array;
    }
}
