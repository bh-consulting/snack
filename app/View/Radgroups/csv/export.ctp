<?php

foreach ($groupsData as $groupData) {
    $this->Csv->addRow($groupData);
}

echo $this->Csv->render($filename);
?> 
