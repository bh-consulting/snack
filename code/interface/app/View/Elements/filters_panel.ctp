<?php
	echo '<div class="toggleBlock" onclick="toggleBlock(this)">';
	echo $this->Html->link(__('Filters'), '#');

    $url = array();
	$params = explode('/', $controller);

    if (isset($params[0])) {
        $url['controller'] = $params[0];
    }
    if (isset($params[1])) {
        $url['action'] = $params[1];
    }
    if (isset($params[2])) {
        $url[] = $params[2];
    }


	if (count($this->params['url']) > 0) {
		echo ' - ';
        echo $this->Html->link(
            __('No filters'),
            $url,
            array('id' => 'nofilters')
        );
	}

	echo '<i class="icon-chevron-down"></i>';
	echo '</div>';

	echo $this->Form->create(null, array(
		'url' => $url,
		'type' => isset($method) ? $method : 'get',
		'id' => 'filtersForm',
		'class' => 'well',
		'style' => $filtersPanOpen ? 'display: block' : null
	));

	foreach ($inputs as $input) {
		$options = array(
			'label' => $input['label'],
			'class' => isset($input['type']) ? $input['type'] : null,
			'multiple' => isset($input['multiple']) ? $input['multiple'] : false,
			'escape' => isset($input['escape']) ? $input['escape'] : true,
		);

        $data = $input['name'] . 'Data';

        if (isset($input['autoComplete'])
            && $input['autoComplete']
            && isset(${$data})
        ) {
            $options['data-provide'] = 'typeahead';
            $options['data-source'] = '["' . implode(${$data}, '","') . '"]';
            $options['data-items'] = 4;
            $options['autocomplete'] = 'off';
        }

		if (isset($input['options'])) {
			$options = array_merge($input['options'], $options);
        }

		echo $this->Form->input($input['name'], $options);
	}

	echo $this->Form->end(__('Search'));
?>
