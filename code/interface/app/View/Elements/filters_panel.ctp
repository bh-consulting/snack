<?php
	echo '<div id="filtersPan" onclick="toggleFiltersPan()">';
	echo $this->Html->link(__('Filters'), '#');

	if(count($this->params['url']) > 0) {
		echo ' - ';
		echo $this->Html->link(__('No filters'), '.', array('id' => 'nofilters'));
	}

	echo '<i class="icon-chevron-down"></i>';
	echo '</div>';

	$controller = explode('/', $controller);

	echo $this->Form->create(null, array(
		'url' => array(
			'controller' => $controller[0],
			'action' => $controller[1]
		),
		'type' => isset($method) ? $method : 'get',
		'id' => 'filtersForm',
		'class' => 'well',
		'style' => $filtersPanOpen ? 'display: block' : null
	));

	foreach($inputs AS $input) {
		$options = array(
			'label' => $input['label'],
			'class' => isset($input['type']) ? $input['type'] : null
		);

		if(isset($input['options']))
			$options = array_merge($input['options'], $options);

		echo $this->Form->input($input['name'], $options);
	}

	echo $this->Form->end(__('Search'));
?>
