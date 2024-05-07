<?php

namespace Jegulnomic\Systems;

interface StorageInterface
{
    public function save(object $object);

    public function get(string $class, string $condition = '', array $bindParameters = []);
}
