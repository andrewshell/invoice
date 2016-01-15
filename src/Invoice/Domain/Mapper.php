<?php declare(strict_types = 1);

namespace Invoice\Domain;

interface Mapper
{
    public function all(): array;
    public function byNumber(string $number): array;
}
