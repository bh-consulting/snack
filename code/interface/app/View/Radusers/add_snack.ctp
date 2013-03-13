<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo '<h1>' . __('Add an admin user') . '</h1>';

echo $this->Form->create('Raduser', array('novalidate' => true));

$userInfo = '<fieldset>';
$userInfo .= '<legend>' . __('User info') . '</legend>';
$userInfo .= $this->element('tab_panes', array(
    'items' => array(
        __('New') => $this->Form->input('username'),
        __('Existing') => $this->Form->input(
            'existing_user',
            array(
                'type' => 'select',
                'options' => $users,
                'empty' => true,
                'label' => __('Existing user')
            )
        ),
    ),
));

$userInfo .= $this->Form->input(
    'passwd',
    array('type' => 'password', 'label' => __('Password'))
);
$userInfo .= $this->Form->input(
    'confirm_password',
    array('type' => 'password', 'label' => __('Confirm password'))
);
$userInfo .= '</fieldset>';

$role = $this->element('snack_role_input');

$finish = $this->Form->end(array(
    'label' => __('Create'),
    'class' => 'next finish',
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
$(document).ready(function(){
    $('input.form-error').first().each(function(){
        var pos = $(this).parents('div.tab-pane').attr('id');
        $('#rootwizard').bootstrapWizard('show', pos.substr(-1));
        $(this).focus();
    });
});
</script>
<?php
$this->end();
?>
