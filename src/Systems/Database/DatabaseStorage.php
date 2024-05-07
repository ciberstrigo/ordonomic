<?php

namespace Jegulnomic\Systems\Database;

use Jegulnomic\Systems\Database\Attributes\Column;
use Jegulnomic\Systems\Database\Attributes\Table;
use Jegulnomic\Systems\StorageInterface;
use PDO;
use ReflectionClass;
use ReflectionProperty;

class DatabaseStorage implements StorageInterface
{
    private static ?self $instance = null;

    private PDO $pdo;

    public static function i(): self
    {
        if (self::$instance) {
            return self::$instance;
        }

        return self::$instance = new DatabaseStorage();
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    public function __construct()
    {
        $this->pdo = new PDO($_ENV['DATABASE_DSN'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function save(object $object): int|string
    {
        $mapped = $this->getPropertiesMapping($object);
        $mappedKeys = array_keys($mapped);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s',
            $this->getTableName($object), // table name
            $this->getQueryTableFields($object), // fields
            implode(', ', array_map(function ($key) {
                return ':'.$key;
            }, $mappedKeys)), // values
            implode(', ', array_map(function ($key) {
                return sprintf('`%s` = VALUES(`%s`)', $key, $key);
            }, $mappedKeys)) // values on duplicate
        );

        $stmt = $this->pdo->prepare($sql);

        foreach ($mapped as $key => &$value) {
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            }

            if (is_object($value) && property_exists($value, 'id')) {
                $value = $this->save($value);
            }

            if (is_bool($value)) {
                $value = (int) $value;
            }

            $stmt->bindParam($key, $value);
        }

        $stmt->execute();

        return $object->id;
    }

    public function saveMany(array $collection): void
    {
        if (empty($collection)) {
            return;
        }

        $placeholdersCommas = implode(
            ', ',
            array_fill(0, $this->getPropertiesCount($collection[0]), '?')
        );

        $placeHoldersGroups = implode(
            ', ',
            array_fill(0, count($collection), sprintf('(%s)', $placeholdersCommas))
        );

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES %s',
            $this->getTableName($collection[0]),
            $this->getQueryTableFields($collection[0]),
            $placeHoldersGroups
        );

        $allValues = [];

        foreach ($collection as $item) {
            $mapped = $this->getPropertiesMapping($item);
            $bound = $this->getBoundParameters($mapped);
            $allValues = array_merge($allValues, array_values($bound));
        }

        $statement = $this->pdo->prepare($sql);
        $isSuccess = $statement->execute($allValues);

        if (!$isSuccess) {
            throw new \RuntimeException('Failed to save collection. ');
        }
    }

    public function get(string $class, string $condition = '', array $bindParameters = []): array
    {
        $mapped = $this->getPropertiesMapping($class);

        $sql = sprintf(
            'SELECT %s FROM %s %s',
            implode(
                ', ',
                array_map(fn ($column) => sprintf('`%s`', $column), array_keys($mapped))
            ),
            $this->getTableName($class),
            $condition
        );

        $stm = $this->pdo->prepare($sql);

        foreach ($bindParameters as $paramName => $paramValue) {
            $stm->bindParam($paramName, $paramValue);
        }

        $stm->execute();
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
        $reflectionClass = new ReflectionClass($class);
        $reverseMapping = array_flip($mapped);

        $result = [];
        foreach ($rows as $row) {
            $args = [];
            $inConstructor = [];
            foreach ($reflectionClass->getConstructor()->getParameters() as $parameter) {
                $fieldName = $reverseMapping[$parameter->getName()];
                $type = $parameter->getType();
                $value = $row[$fieldName];

                if ($type->getName() === \DateTimeInterface::class) {
                    $convertedPropertyValue = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row[$fieldName]);

                    if (false === $convertedPropertyValue) {
                        $convertedPropertyValue = \DateTimeImmutable::createFromFormat('Y-m-d', $row[$fieldName]);
                    }

                    $row[$fieldName] = $convertedPropertyValue;
                }

                if (class_exists($type->getName())) {
                    // TODO так нельзя, надо гигазапрос херачить.
                    $row[$fieldName] = $this->getOne($type->getName(), 'WHERE id = :value', [':value' => $value]);
                }

                $args[] = $row[$fieldName];
                $inConstructor[] = $fieldName;
            }

            $extractName = fn ($element) => $element->getName();
            $x = array_map($extractName, $reflectionClass->getConstructor()->getParameters());
            $y = array_map($extractName, $reflectionClass->getProperties());
            $notInConstructor = array_diff($y, $x);
            $newClass = new $class(...$args);

            foreach ($notInConstructor as $props) {
                $propertyName = $reverseMapping[$props];
                $methodName = sprintf('set%s', ucfirst($props));
                if (method_exists($class, $methodName)) {
                    $newClass->$methodName($row[$reverseMapping[$props]]);
                }
            }

            $result[] = $newClass;
        }

        return $result;
    }

    public function getOne(string $class, string $condition = '', array $bindParameters = []): ?object
    {
        $condition = preg_replace('/ LIMIT [0-9]/i', ' ', $condition) . ' LIMIT 1';
        $result = $this->get($class, $condition, $bindParameters);

        if (empty($result)) {
            return null;
        }

        return $result[0];
    }

    private function getBoundParameters(array $mapping): array
    {
        $result = [];

        foreach ($mapping as $key => &$value) {
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            }

            if (is_object($value) && property_exists($value, 'id')) {
                $value = $this->save($value);
            }

            if (is_bool($value)) {
                $value = (int) $value;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    private function getQueryTableFields(object $object): string
    {
        $keys = array_keys($this->getPropertiesMapping($object));
        $keys = array_map(fn ($name) => sprintf('`%s`', $name), $keys);

        return implode(', ', $keys);
    }

    private function getTableName(mixed $object): string
    {
        $reflectionClass = new ReflectionClass($object);

        foreach ($reflectionClass->getAttributes() as $attribute) {
            if ($attribute->getName() === Table::class) {
                return $attribute->getArguments()['name'];
            }
        }

        return $reflectionClass->getName();
    }

    private function getPropertiesMapping(mixed $object): array
    {
        $reflectionClass = new ReflectionClass($object);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

        $mapped = [];

        foreach ($properties as $property) {
            $attributes = $property->getAttributes();
            foreach ($attributes as $attribute) {
                if ($attribute->getName() === Column::class) {
                    if (is_object($object)) {
                        $mapped[$attribute->getArguments()['name'] ?? $property->getName()] = $property->getValue($object);
                    } elseif (is_string($object)) {
                        $mapped[$attribute->getArguments()['name'] ?? $property->getName()] = $property->getName();
                    }
                }
            }
        }

        return $mapped;
    }

    private function getPropertiesCount(mixed $object): int
    {
        $reflectionClass = new ReflectionClass($object);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

        return count($properties);
    }

    private function getColumnRelatedTo(\ReflectionParameter $parameter): ?string
    {
        $attributes = $parameter->getAttributes();

        foreach ($attributes as $attribute) {
            $arguments = $attribute->getArguments();

            if (
                $attribute->getName() === Column::class
                && array_key_exists('relatedTo', $arguments)
            ) {
                return $arguments['relatedTo'];
            }
        }

        return null;
    }
}
