<?php

namespace ImportBundle\Services;

use SplFileObject;

class ImportCSV
{
    protected $file_content;
    protected $header;

    public function startImport($filename)
    {
        if ($filename) {
            // Open the File
            $file = new SplFileObject($filename);
            $file->setFlags(SplFileObject::READ_CSV);

            $headerArray = '';
            $rowCounter = 0;

            foreach ($file as $row) {
                if ($this->header == 1 && $rowCounter == 0) {
                    foreach ($row as $fields) {
                        $headerArray[] = $fields;
                    }
                } else {
                    $this->addContent($headerArray, $row);
                }

                $rowCounter++;
            }
        }

        return true;
    }

    public function getContent()
    {
        return $this->file_content;
    }

    public function setHeader($headerExists)
    {
        $this->header = $headerExists;
    }

    private function addContent($headerArray, $fileContent)
    {
      $content = '';
      if( count($headerArray) == count($fileContent) ) {
        for( $i = 0; $i < count($headerArray); $i++ ) {
          $content[ $headerArray[$i] ] = $fileContent[$i];
        }
        $this->file_content[] = $content;
      } else {

      }

    }
}
