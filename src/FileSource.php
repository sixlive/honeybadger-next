<?php

namespace Honeybadger;

use SplFileObject;

class FileSource
{

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $lineNumber;

    /**
     * @var integer
     */
    protected $radius;

    /**
     * @param  string  $filename
     * @param  integer  $lineNumber
     * @param  integer  $radius
     */
    public function __construct(string $filename, int $lineNumber, int $radius = 4)
    {
        $this->filename = $filename;
        $this->lineNumber = $lineNumber < 0 ? 0 : $lineNumber;
        $this->radius = $radius;
    }

    /**
     * @return array
     */
    public function getSource() : array
    {
        if (! $this->canReadFile()) {
            return [];
        }

        return array_slice(
            $this->fileLines($this->readFile()),
            $this->startingLineNumber($this->lineNumber, $this->radius),
            ($this->radius * 2) + 1,
            $preserveKeys = true
        );
    }

    /**
     * @param  string  $line
     * @return string
     */
    private function trimLine(string $line) : string
    {
        $trimmed = trim($line, "\n\r\0\x0B");

        return preg_replace(['/\s*$/D', '/\t/',], ['', '    ',], $trimmed);
    }

    /**
     * @return boolean
     */
    private function canReadFile() : bool
    {
        return is_file($this->filename) && is_readable($this->filename);
    }

    /**
     * @return \SplFileObject
     */
    private function readFile() : SplFileObject
    {
        return new SplFileObject($this->filename, 'r');
    }

    /**
     * @param  \SplFileObject  $file
     * @return array
     */
    private function fileLines(SplFileObject $file) : array
    {
        $lines = [];
        while (! $file->eof()) {
            $lines[] = $this->trimLine($file->fgets());
        }

        // Set the array to base 1 so it actually reflects the line number of code
        array_unshift($lines, null);
        unset($lines[0]);

        return $lines;
    }

    /**
     * @return integer
     */
    private function startingLineNumber() : int
    {
        $start = $this->lineNumber - ($this->radius + 1);

        return $start >= 0 ? $start : 0;
    }
}
