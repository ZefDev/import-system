<?php

namespace App\Interfaces;

interface FileAnalyzerInterface
{
    public function analyze(array $data): array;
}