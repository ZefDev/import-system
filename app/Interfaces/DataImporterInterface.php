<?php

namespace App\Interfaces;

interface DataImporterInterface
{
    public function readDataFromFile() : array;
}