<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo '<h1>' . __('Add a SNACK user') . '</h1>';

$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Raduser', array(
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

$userInfo = '<fieldset>';
$userInfo .= '<legend>' . __('User info') . '</legend>';

$myLabelOptions = array('text' => __('Password'));
$passwords = $this->Form->input('passwd', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$myLabelOptions = array('text' => __('Confirm Password'));
$passwords .= $this->Form->input('confirm_password', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$userInfo .= $this->element('tab_panes', array(
    'items' => array(
        __('New') => $this->Form->input('username') . $passwords,
        __('Existing') => $this->Form->input('existing_user', array(
            'label' => array_merge($mainLabelOptions, $myLabelOptions),
            'options' => array_merge(array('' => __('Select a user...')), $users),
            //'disabled' => true,
            'empty' => false,
        )) . '<div id="passwords">' . $passwords . '</div>',
    ),
));
$userInfo .= '</fieldset>';

$role = $this->element('snack_role_input');

$finish = $this->Form->end(array(
    'label' => __('Create'),
    'class' => 'next finish btn btn-primary',
    'style' => 'display:none;',
));

echo $this->element('wizard', array(
    'steps' => array(
        __('User info') => $userInfo,
        __('Role') => $role,
    ),
    'finishButton' => $finish,
));

$this->start('script');
?>
<script>
$('#RaduserExistingUser').change(function() {
    $('#RaduserExistingUser option:selected').first().each(function() {
        if ($(this).attr('data-pwd') && $(this).attr('data-pwd') == 1) {
            $('#passwords').slideDown();
        } else {
            $('#passwords').slideUp();
        }
    });
});

$(document).ready(function(){
    $('input.form-error').first().each(function(){
        var pos = $(this).parents('div.tab-pane').attr('id');
        $('#rootwizard').bootstrapWizard('show', pos.substr(-1));
        $(this).focus();
    });
<?php
if (empty($this->request->data['Raduser']['existing_user'])) {
?>
    $('#RaduserExistingUser').val('');
<?php
} else {
?>
    $('#subtab a[href="#tab2"]').tab('show');
<?php
}
?>
});
</script>
<?php
echo $this->Html->script('wizard_focus');
$this->end();
?>
