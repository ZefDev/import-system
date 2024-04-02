<?php

namespace App\Interfaces;

interface ProductFilterRule
{
    public function filter(array $item): bool;
}