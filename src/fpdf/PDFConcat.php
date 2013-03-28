<?php
class PDFConcat extends fpdi
{
	protected $_documents = array();
	protected $_tmpDocuments = array();
	
	public function __construct($orientation ='P', $unit ='mm', $format ='A4')
	{
	    parent::fpdi($orientation, $unit, $format);
	}
	
	public function setDocuments(array $documents)
	{
		foreach ($documents as $document) {
			$this->addDocument($document);
		}
	}
	
	public function addDocument($document)
	{
		// PDF muss erst gepsiehcret werden damit es zusammengefügt werden kann.
        $filename = sprintf('%s/shared/concat_%s.pdf', BASE_PATH, md5(uniqid()));

        $fp = fopen($filename, 'w');
        fwrite($fp, $document);
        fclose($fp);
        $this->_documents[] = $filename;
        $this->_tmpDocuments[] = $filename;
	}
	
	public function output($name='',$dest='')
	{
		foreach($this->_documents AS $document) {
		    $pageCount = $this->setSourceFile($document);
		    
		    for ($i = 1; $i <= $pageCount; $i++) {
		         $tplidx = $this->ImportPage($i);
		         $this->AddPage();
		         $this->useTemplate($tplidx);
		    }
		}

		foreach ($this->_tmpDocuments as $document) {
			unlink($document);
		}
		
		parent::Output($name,$dest);
	}
}