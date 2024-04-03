<?php

namespace App\Interfaces;

interface FileReportGeneratorInterface
{
    public static function generate(array $analysis): string;
}