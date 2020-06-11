<?php

namespace Csv\Converter;

use Generator;

abstract class Converter
{
    abstract public function convert($resource, int $rowSkip = 0) : string;

    protected function getCsvRows($resource, int $rowSkip = 0) : Generator
    {
        for ($x = 0; $x < $rowSkip; $x++) {
            fgetcsv($resource);
        }

        while (($row = fgetcsv($resource)) !== false) {
            yield $row;
        }
    }
}