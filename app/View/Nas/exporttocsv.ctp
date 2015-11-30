<?php
$this->Csv->addRow(array("Nasname", "Shortname", "Description", "Secret", "Login", "Password", "Enablepassword", "Backup"));
foreach ($nasData as $naData) {
    $this->Csv->addRow($naData);
}

echo $this->Csv->render($filename);
?>
