<?php

namespace Command;

use Csv\Converter\Converter;
use Csv\Converter\PhpArrayConverter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertCsvCommand extends Command
{
    protected static $defaultName = "convert:csv";

    const FORMAT_TO_CONVERTER = [
        "array" => PhpArrayConverter::class
    ];

    protected function configure()
    {
        $this->setDescription("Convert a CSV into one of the provided output options")
            ->addArgument("input", InputArgument::REQUIRED, "CSV Path to be converted.")
            ->addArgument("format", InputArgument::REQUIRED, "Format to conver the CSV to. (array)")
            ->addOption("row-skip", 0, InputOption::VALUE_REQUIRED, "Number of rows to skip.")
            ->addOption("output", "out", InputOption::VALUE_REQUIRED, "Output filename.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputPath = $input->getArgument("input");
        $conversionFormat = $input->getArgument("format");

        if (!file_exists($inputPath)) {
            $output->writeln("<error>Input file could not be found at provided path.</error>");
            return Command::FAILURE;
        }

        if (!in_array($conversionFormat, array_keys(static::FORMAT_TO_CONVERTER))) {
            $output->writeln("<error>Specified format is not supported.</error>");
            return Command::FAILURE;
        }

        $resource = fopen($inputPath, 'r');
        $converted = $this->getConverter($conversionFormat)->convert($resource, $input->getOption("row-skip"));
        
        file_put_contents($input->getOption("output"), $converted);

        return Command::SUCCESS;
    }

    protected function getConverter(string $conversionFormat) : Converter
    {
        $conversionFormatClass = static::FORMAT_TO_CONVERTER[$conversionFormat];
        return new $conversionFormatClass();
    }
}