<?php 

/**
 * A buffered file writer
 */
class FileWriter
{
    private $fileHandle;

    /**
     * FileWriter constructor.
     */
    public function __construct($filename)
    {
        if(file_exists($filename))
            die("File already exists: " . $filename);

        $this->fileHandle = fopen($filename, 'w');

        if($this->fileHandle === false)
            die("Could not open file: " . $filename);
    }

    function add($data) {
        if(gettype($data) === "array") {
            foreach($data as $line) {
                $this->internalAddLine($line . "\n");
            }
        } else {
            $this->internalAddLine($data);
        }
    }

    function internalAddLine($data) {
        fwrite($this->fileHandle, $data);
    }

    function done() {
        fclose($this->fileHandle);
    }
}