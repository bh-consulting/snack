<?php 

$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('groups_active', 'active');

echo '<h1>' . __('Add a group') . '</h1>';

$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Radgroup', array(
    'novalidate' => true, 
    'autocomplete' => 'off',
    'class' => 'form-horizontal',
    'inputDefaults' => array(
        'div' => 'form-group',
        'label' => array(
            'class' => $mainLabelOptions
        ),
        'between' => '<div class="col-sm-4">',
        'after'   => '</div>',
        'class' => 'form-control'
    ),
));
//echo $this->Form->create('Radgroup', array('novalidate' => true));

$info = '<fieldset>';
$info .= '<legend>' . __('Information') . '</legend>';
$myLabelOptions = array('text' => __('Name'));
$info .= $this->Form->input('groupname', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$info .= $this->element('doubleListsSelector', array('leftTitle' => __('Users'), 'rightTitle' => __('Selected users'), 'contents' => $users, 'comments' => $comments, 'selectedContents' => array()));
$info .= $this->Form->input('users', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));
$info .= '</fieldset>';

$checks = '<fieldset>';
$checks .= '<legend>' . __('Checks') . '</legend>';
$checks .= $this->element('check_common_fields');
$checks .= '</fieldset>';

$replies = '<fieldset>';
$replies .= '<legend>' . __('Replies') . '</legend>';
$replies .= $this->element('reply_common_fields');
$replies .= '</fieldset>';

$finish = $this->Form->end(array(
    'label' => __('Create'),
    'class' => 'next finish btn btn-primary',
    'style' => 'display:none;',
));

echo $this->element('wizard', array(
    'steps' => array(
        __('Info') => $info,
        __('Checks') => $checks,
        __('Replies') => $replies,
    ),
    'finishButton' => $finish,
));

$this->start('script');
echo $this->Html->script('wizard_focus');
$this->end();

?>
