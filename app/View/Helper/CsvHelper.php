<?php 

class CsvHelper extends AppHelper {

    private $delimiter;
    private $enclosure;
    private $filename;
    private $line;
    private $buffer;

    function CsvHelper() {
        $this->clear();
    }

    function clear() {
        $this->delimiter = ',';
        $this->enclosure = '"';
        $this->filename = 'export.csv';
        $this->line = array();
        $this->buffer = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
    }

    function addField($value) {
        $this->line[] = $value;
    }

    function endRow() {
        $this->addRow($this->line);
        $this->line = array();
    }

    function addRow($row) {
        fputcsv($this->buffer, $row, $this->delimiter, $this->enclosure);
    }

    function renderHeaders() {
        header("Content-type:application/vnd.ms-excel");
        header("Content-disposition:attachment;filename=".$this->filename);
    }

    function setFilename($filename) {
        $this->filename = $filename;

        if (strtolower(substr($this->filename, -4)) != '.csv') {
            $this->filename .= '.csv';
        }
    }

    function render($headers = true, $toEncoding = null, $fromEncoding = "auto") {
        if ($headers) {
            if (is_string($headers)) {
                $this->setFilename($headers);
            }
            $this->renderHeaders();
        }

        rewind($this->buffer);

        $output = stream_get_contents($this->buffer);

        if ($toEncoding) {
            $output = mb_convert_encoding(
                $output,
                $toEncoding,
                $fromEncoding
            );
        }

        return $this->output($output);
    }
}

?> 
