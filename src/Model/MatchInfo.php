<?php

namespace Tug\HttpCacheBundle\Model;

use DateTimeInterface;
use Exception;

class MatchInfo implements \Serializable
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


    public function serialize(): string
    {
        $parts = [ $this->getETag() ];

        $modifiedDate = $this->getModifiedDate();

        if ($modifiedDate !== null) {
            $parts[] = $modifiedDate->getTimestamp();

            $timezone = $modifiedDate->getTimezone();
            if ($timezone !== false) {
                $parts[] = $timezone->getName();
            }
        }

        return implode(':', $parts);
    }

    /**
     * @throws Exception
     */
    public function unserialize(string $data): void
    {
        list($eTag, $modifiedDate, $timezone) = explode(':', $data);

        $this->setETag($eTag);

        if (!empty($modifiedDate)) {
            $this->setModifiedDate(\DateTimeImmutable::createFromFormat('U', $modifiedDate,
                !empty($timezone) ? new \DateTimeZone($timezone) : null));
        }
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