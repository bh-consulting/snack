<?php
	$this->extend('/Common/radius_sidebar');
	$this->assign('logs_active', 'active');

	$columns = array(
		'id' => 'Line',
		'datetime' => 'Date',
		'level' => 'Level',
		'msg' => 'Message'
	);
?>

<h1>Logs</h1>

<table class="table">
	<thead>
	<tr>
	    <?php
		foreach($columns as $field => $text) {
			$sort = preg_match("#$field$#", $this->Paginator->sortKey()) ?  $this->Html->tag('i', '', array('class' => $sortIcons[$this->Paginator->sortDir()])) : '';

			echo "<th>";
			echo $this->Paginator->sort($field, "$text $sort", array('escape' => false));
			echo "</th>";
		}
	    ?>
	</tr>
	</thead>

	<tbody>
	<?php if(!empty($loglines)): ?>
		<?php
			foreach($loglines AS $logline) {
				echo '<tr>';

				foreach($columns as $field => $text)
					echo "<td>{$logline['Logline'][$field]}</td>";

				echo '</tr>';
			}
		?>
	<?php else: ?>
		<tr>
		<td colspan="2">
			No logs found.
		</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
<?php
	$paginate = $this->Paginator->prev('Prev.', array(), null, array('class' => 'disabled')).$this->Paginator->numbers(array(
		'modulus' => 2,
		'first' => 2,
		'last' => 2,
		'ellipsis' => "<span class='disabled'>...</span>",
		'separator' => '',	
		'currentClass' => 'disabled'

	)).$this->Paginator->next('Next', array(), null, array('class' => 'disabled'));

	echo $this->Html->tag('div', $paginate, array('class' => 'pagination pagination-small', 'style' => 'float:left;'));
?>
<br />
<div style="float:right;">
<?php
	echo $this->Paginator->counter(array('format' => 'range'));
?>
</div>
