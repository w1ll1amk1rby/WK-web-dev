<?php

function generateNewXMLDocument($townName) {
    $reader = new XMLReader();
    $writer = new XMLWriter();
    $inputFileName = $townName . '.xml';
    $outputFileName = $townName . '_no2' . '.xml';
    $reader->open($inputFileName);
    $writer->openUri($outputFileName);
    $writer->startDocument('1.0','UTF-8');
    $writer->setIndent(4);
    $writer->startElement('data');
    $writer->writeAttribute('type','nitrogen dioxide');
    $firstRow = true;
    while($reader->read()) {
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'row') {
            $nodeHolder = new DOMDocument();
            $node = simplexml_import_dom($nodeHolder->importNode($reader->expand(), true));
            // add location if the first its the first time doing this
            if($firstRow == true) {
                $desc = $node->desc;
                $lat = $node->lat;
                $long = $node->long;
                $writer->startElement('location'); // add town location
                $writer->writeAttribute('id',$desc);
                $writer->writeAttribute('lat',$lat);
                $writer->writeAttribute('long',$long);
                $writer->endElement(); // end location
                $firstRow = false;
            }
            // add location
            $date = $node->date;
            $time = $node->time;
            $val = $node->no2;
            $writer->startElement('reading');
            $writer->writeAttribute('date',$date);
            $writer->writeAttribute('time',$time);
            $writer->writeAttribute('val',$val);
            $writer->endElement(); // end reading
        }
    }
    $writer->endElement(); // end data
    $writer->endDocument();
    $writer->flush();
}
echo "WORKING ON IT ";
// for every town create a document
$townNames = array('brislington','fishponds','parson_st','rupert_st','wells_rd','newfoundland_way');
for($x = 0;$x < count($townNames);$x++) {
    generateNewXMLDocument($townNames[$x]);
}
echo 'DONE';