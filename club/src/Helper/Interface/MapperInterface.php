<?php

namespace App\Helper\Interface;

interface MapperInterface
{
    public function dtoToEntity($dto, $entity = null);
    public function entityToDTO($entity);
}