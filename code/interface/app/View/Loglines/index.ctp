<?php
	$this->extend('/Common/radius_sidebar');
	$this->assign('logs_active', 'active');

	$columns = array(
		'id' => __('Line'),
		'datetime' => __('Date'),
		'level' => __('Severity'),
		'msg' => __('Message')
	);
?>

<h1><? echo __('Logs'); ?></h1>

<?php
	echo $this->element('filters_panel', array(
		'controller' => 'loglines/index',
		'inputs' => array(
			array(
				'name' => 'level',
				'label' => __('Severity from'),
				'type' => 'slidermax',
				'options' => array('id' => 'severity')
			),
			array(
				'name' => 'datefrom',
				'label' => __('From'),
				'type' => 'datetimepicker',
				'options' => array('id' => 'datefrom')
			),
			array(
				'name' => 'dateto',
				'label' => __('To'),
				'type' => 'datetimepicker',
				'options' => array('id' => 'dateto')
			),
			array(
				'name' => 'msg',
				'label' => __('Message contains (accept regex)'),
				'options' => array('id' => 'logmessage')
			))
	));
?>

<table class="table loglinks">
	<thead>
	<tr>
	    <?php
		foreach($columns as $field => $text) {
			$sort = preg_match("#$field$#", $this->Paginator->sortKey()) ? $this->Html->tag('i', '', array('class' => $sortIcons[$this->Paginator->sortDir()])) : '';

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
				echo "<tr class='loglevel{$logline['Logline']['level']}'>";

				foreach($columns as $field => $text) {
					echo "<td class='logcell$field'>";

					if($field == 'datetime')
						echo $this->Html->link(__($logline['Logline'][$field]), '#', array('onclick' => 'logsSearchFromDate($(this))', 'title' => __('Search from this date')));
					else if($field == 'level')
						echo $this->Html->link(__($logline['Logline'][$field]), '#', array('onclick' => 'logsSearchFromSeverity($(this))', 'title' => __('Search from this severity')));
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
				echo __('No logs found').' (';

				if(count($this->params['url']) > 0)
					echo $this->Html->link(__('retry with no filters'), '.');
				else
					echo __('no filters');

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
