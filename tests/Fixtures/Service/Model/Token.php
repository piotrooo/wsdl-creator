<?php

namespace Fixtures\Service\Model;

class Token
{
    private string $name;
    private int $expire;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Token
    {
        $this->name = $name;
        return $this;
    }

    public function getExpire(): int
    {
        return $this->expire;
    }

    public function setExpire(int $expire): Token
    {
        $this->expire = $expire;
        return $this;
    }
}
