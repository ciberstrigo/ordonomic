<?php

namespace Jegulnomic\Systems;

interface StorageInterface
{
    public function save(object $object);

    public function saveMany(array $collection);

    public function get(string $class, string $condition = '', array $bindParameters = []);

    public function getOne(string $class, string $condition = '', array $bindParameters = []);
}
