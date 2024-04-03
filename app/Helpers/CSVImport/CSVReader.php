<?php

namespace App\Helpers\CSVImport;

use App\Interfaces\FileReaderInterface;
use League\Csv\Reader;

class CSVReader implements FileReaderInterface
{
    private Reader $reader;
    private string $path;
    private array $fieldMap;

    /**
     * Constructor.
     *
     * @param string $path The path to the CSV file
     * @param array $fieldMap The mapping of CSV fields to model fields
     */
    public function __construct(string $path, array $fieldMap)
    {
        $this->path = $path;
        $this->fieldMap = $fieldMap;
    }

    /**
     * Open the CSV file.
     *
     * @param string $filePath The path to the CSV file
     * @return void
     */
    public function openFile(string $filePath): void
    {
        $reader = Reader::createFromPath($this->path, 'r');
        $this->reader = $reader;
    }

    /**
     * Read the CSV file and map its fields to model fields.
     *
     * @return array The data read from the CSV file
     */
    public function readFile(): array
    {
        // Open the CSV file
        $this->openFile($this->path);

        // Set header offset to skip the header row
        $this->reader->setHeaderOffset(0);

        // Get the records from the CSV file
        $records = $this->reader->getRecords();
        $data = [];

        // Iterate through each record
        foreach ($records as $record) {
            $modelData = [];

            // Map CSV fields to model fields
            foreach ($this->fieldMap as $csvFieldName => $modelFieldName) {
                // Check if the CSV field exists in the record
                if (array_key_exists($csvFieldName, $record)) {
                    // Map the CSV field to the corresponding model field
                    $modelData[$modelFieldName] = $record[$csvFieldName];
                }
            }

            // Add the mapped data to the result array
            $data[] = $modelData;
        }

        return $data;
    }

    /**
     * Set the path to the CSV file.
     *
     * @param string $path The path to the CSV file
     * @return void
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Get the path to the CSV file.
     *
     * @return string The path to the CSV file
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
