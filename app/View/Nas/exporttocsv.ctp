<?php
$this->Csv->addRow(array("Nasname", "Shortname", "Description", "Version", "Image", "Serialnumber", "Model"));
foreach ($nasData as $naData) {
    $this->Csv->addRow($naData);
}

echo $this->Csv->render($filename);
?>
