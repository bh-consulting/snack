<div id="topology">

<?php 
echo '<div class="toggleBlock" onclick="toggleBlock(this)">';
echo $this->Html->link(__("Topology of $date"), '#');
echo '<i class="glyphicon glyphicon-chevron-down"></i>';
echo '</div>';
?>
<pre>
<?php
echo $return;
?>
</pre>
</div>