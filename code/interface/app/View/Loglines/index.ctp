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
	echo $this->Html->link(__('Filters', true), '#', array('onclick' => 'logsToggleSearch()'));

	if(count($this->params['url']) > 0) {
		echo ' - ';
		echo $this->Html->link(__('No filters', true), '.', array('style' => 'font-weight:bold'));
	}

	echo '<br /><br />';

	echo $this->Form->create(null, array(
		'url' => array(
			'controller' => 'loglines',
			'action' => 'index'
		),
		'type' => 'get',
		'id' => 'logsSearchForm',
		'style' => 'display:none',
		'class' => 'well'
	));

	echo $this->Form->input('severity', array('label' => 'Severity from', 'id' => 'severity'));
	echo $this->Form->input('datefrom', array('label' => 'From', 'class' => 'datetimepicker', 'id' => 'datefrom'));
	echo $this->Form->input('dateto', array('label' => 'To', 'class' => 'datetimepicker', 'id' => 'dateto'));
	echo $this->Form->input('message', array('label' => 'Message contains (accept regex)', 'style' => 'margin-top:12px'));

	echo $this->Form->end('Search');
?>

<table class="table">
	<thead>
	<tr>
	    <?php
		foreach($columns as $field => $text) {
			$sort = preg_match("#$field$#", $this->Paginator->sortKey()) ?  $this->Html->tag('i', '', array('class' => $sortIcons[$this->Paginator->sortDir()])) : '';

			echo '<th>';
			echo $this->Paginator->sort($field, "$text $sort", array('escape' => false));
			echo '</th>';
		}
	    ?>
	</tr>
	</thead>

	<tbody>
	<?php if(!empty($loglines)): ?>
		<?php
			foreach($loglines AS $logline) {
				echo '<tr>';

				foreach($columns as $field => $text) {
					echo '<td>';

					if($field == 'datetime')
						echo $this->Html->link(__($logline['Logline'][$field], true), '#', array('onclick' => 'logsSearchFromDate($(this))', 'title' => 'Search from this date'));
					else if($field == 'level')
						echo $this->Html->link(__($logline['Logline'][$field], true), '#', array('onclick' => 'logsSearchFromSeverity($(this))', 'title' => 'Search from this severity'));
					else
						echo $logline['Logline'][$field];

					echo '</td>';
				}

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
