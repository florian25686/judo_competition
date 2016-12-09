<?php

namespace ImportBundle\Services;

use SplFileObject;

class ImportCSV {
    
    protected $file_content;
    protected $header;
    
    public function startImport($filename)
    {
        if ( $filename )
        {
            // Open the File
            $file = new SplFileObject($filename);
            $file->setFlags(SplFileObject::READ_CSV);
            
            $headerArray = '';
            $rowCounter = 0;
            print "header:".$this->header."\\";
            foreach ($file as $row) {
                
                if ($this->header == 1 && $rowCounter == 0) {
                    foreach($row as $fields) {
                        $headerArray[] = $fields;
                       
                    }
                } else {
                    $this->addContent( $row );
                    
                }
                
                $rowCounter++;
            }
        }
        
        return true;
    }
    
    public function getContent() {
        return $this->file_content;
    }
    
    /**
     * 
     * @param boolean $status
     */
    public function setHeader($status) {
        
        $this->header = $status;
        
    }
    private function addContent($content) {
        
        $this->file_content[] = $content;
        
    }
}