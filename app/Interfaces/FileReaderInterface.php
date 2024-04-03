<?php

namespace App\Interfaces;

interface FileReaderInterface
{
    public function openFile(string $filePath): void;
    public function readFile(): array;
}