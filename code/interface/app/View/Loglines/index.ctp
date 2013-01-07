<?php
	$this->extend('/Common/radius_sidebar');
	$this->assign('logs_active', 'active');

	$columns = array(
		'id' => 'Line',
		'datetime' => 'Date',
		'level' => 'Severity',
		'msg' => 'Message'
	);
?>

<h1>Logs</h1>

<?php
	$dateOptions = array(
		'type' => 'datetime',
		'timeFormat' => 24,
		'dateFormat' => 'DMY',
		'minYear' => 2010,
		'maxYear' => date('Y')
	);

	echo $this->Html->link(__('Filters', true), '#', array('onclick' => "\$('#PostSearchForm').toggle()"));

	if(count($this->params['url']) > 0) {
		echo ' - ';
		echo $this->Html->link(__('No filters', true), '.', array('style' => 'font-weight: bold'));
	}

	echo '<br /><br />';

	echo '<fieldset id="PostSearchForm" style="display:none"><legend>Filters</legend>';
	echo $this->Form->create(null, array('url' => array('controller' => 'loglines', 'action' => 'index'), 'type' => 'get'));
	echo $this->Form->input('severity', array('label' => 'Severity from'));
	echo $this->Form->input('datefrom', array_merge(array('label' => 'From'), $dateOptions));
	echo $this->Form->input('dateto', array_merge(array('label' => 'To'), $dateOptions));
	echo $this->Form->input('message', array('label' => 'Message contains (accept regex)'));
	echo $this->Form->end('Search');
	echo '</fieldset>';
?>

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
		<td colspan="4">
			<?php
				echo 'No logs found (';

				if(count($this->params['url']) > 0)
					echo $this->Html->link(__('retry with no filters', true), '.');
				else
					echo 'no filters';

				echo ').';
			?>
		</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
<?php
echo $this->element('paginator_footer');
?>
</div>
