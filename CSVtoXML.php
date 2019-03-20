<?php
ini_set('max_execution_time', 300);
echo "working .. wait";

function extractTownFromCSVForXML($csv,$townName,$townNumber) {
# define the tags - last col value in csv file is derived so ignore
    $header = array('id', 'desc', 'date', 'time', 'nox', 'no', 'no2', 'lat', 'long');
    # count the number of items in the $header array so we can loop using it
    $cols = count($header);
    #set record count to 1
    $count = 1;
    # set row count to 2 - this is the row in the original csv file
    $row = 2;
    # start ##################################################
    $out = '<records>';
    while (($data = fgetcsv($csv, 200, ",")) !== FALSE) {
        if ($data[0] == $townNumber) {
            $rec = '<row count="' . $count . '" id="' . $row . '">';
            for ($c=0; $c < $cols; $c++) {
                $rec .= '<' . trim($header[$c]) . '>' . trim($data[$c]) . '</' . trim($header[$c]) . '>';
            }
            $rec .= '</row>';
            $count++;
            $out .= $rec;
        }
        $row++;
    }
    $out .= '</records>';
    # finish ##################################################

    # write out file
    file_put_contents($townName . '.xml', $out);
}

ob_flush();

flush();
$townNames = array('brislington','fishponds','parson_st','rupert_st','wells_rd','newfoundland_way');
$townNumbers = array(3,6,8,9,10,11);
// for every town
for($x = 0;$x < count($townNames);$x++) {
    if (($handle = fopen("air_quality.csv", "r")) !== FALSE) {

        # throw away the first line - field name
        fgetcsv($handle, 200, ",");
        extractTownFromCSVForXML($handle, $townNames[$x], $townNumbers[$x]);
        fclose($handle);
    }
}


echo " created town docs!";

?>