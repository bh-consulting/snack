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

    if (count($this->params['url']) > 3) {
		echo ' - ';
        echo $this->Html->link(
            __('No filters'),
            $url,
            array('id' => 'nofilters')
        );
	}

	echo '<i class="glyphicon glyphicon-chevron-down"></i>';
	echo '</div>';

    $mainLabelOptions = array('class' => 'col-sm-4 control-label');
    //debug($url);
    if (isset($host)) {
        $url['action'] = $url['action']."/host:".$host;
    }
	echo $this->Form->create(null, array(
		'url' => $url,
		'type' => isset($method) ? $method : 'get',
		'id' => 'filtersForm',
		'class' => 'well form-horizontal',
        'inputDefaults' => array(
            'div' => 'form-group',
            /*'label' => array(
                'class' => $mainLabelOptions
            ),*/
            'between' => '<div class="col-sm-4">',
            'after'   => '</div>',
            'class' => 'form-control'
        ),
		'style' => (isset($filtersPanOpen) && $filtersPanOpen) ? 'display: block' : null
	));

	foreach ($inputs as $input) {
        $myLabelOptions = array('text' => $input['label']);
		$options = array(
			'label' => array_merge($mainLabelOptions, $myLabelOptions),
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
    $options = array(
    'label' => __('Search'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => 'btn btn-primary',
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
    );
    echo $this->Form->end($options);
?>
