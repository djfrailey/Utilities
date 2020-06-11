<?php

namespace Csv\Converter;

class PhpArrayConverter extends Converter
{
    public function convert($resource, int $rowSkip = 1) : string
    {
        $rows = [];
        foreach ($this->getCsvRows($resource, $rowSkip) as $row) {
            $rows[] = $row;
        }

        return var_export($rows, true);
    }
}