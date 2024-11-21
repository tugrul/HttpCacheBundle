<?php

namespace Tug\HttpCacheBundle\Model;

use DateTimeInterface;

class MatchInfo
{
    protected ?string $eTag;

    protected ?DateTimeInterface $modifiedDate;

    public function __construct(?string $eTag, ?DateTimeInterface $modifiedDate)
    {
        $this->eTag = $eTag;

        $this->modifiedDate = $modifiedDate;
    }

    public function getETag(): ?string
    {
        return $this->eTag;
    }

    public function setETag(?string $eTag): MatchInfo
    {
        $this->eTag = $eTag;
        return $this;
    }

    public function getModifiedDate(): ?DateTimeInterface
    {
        return $this->modifiedDate;
    }

    public function setModifiedDate(?DateTimeInterface $modifiedDate): MatchInfo
    {
        $this->modifiedDate = $modifiedDate;
        return $this;
    }

    public function __serialize(): array
    {
        return [$this->getETag(), $this->getModifiedDate()];
    }

    public function __unserialize(array $data): void
    {
        list($eTag, $modifiedDate) = $data;

        $this->setETag($eTag);
        $this->setModifiedDate($modifiedDate);
    }
}