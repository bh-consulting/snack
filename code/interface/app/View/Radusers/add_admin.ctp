<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo '<h1>' . __('Add an admin user') . '</h1>';

echo $this->Form->create('Raduser');

echo '<fieldset>';
echo '<legend>' . __('User info') . '</legend>';

?>
<div class="tabbable">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab1" data-toggle="tab">
                <?php echo __('New'); ?>
            </a>
        </li>
        <li>
            <a href="#tab2" data-toggle="tab">
                <?php echo __('Existing'); ?>
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab1">
            <?php echo $this->Form->input('username'); ?>
        </div>
        <div class="tab-pane" id="tab2">
            <?php
                echo $this->Form->input('users', array('type' => 'select'));
            ?>
        </div>
    </div>
</div>

<?php
echo $this->Form->input('passwd', array('type' => 'password', 'label' => __('Password')));
echo $this->Form->input('confirm_password', array('type' => 'password'));

echo '</fieldset>';

echo '<fieldset>';
echo '<legend>' . __('Admin rights') . '</legend>';
echo 'Citation CdC : - créer utilisateur
– créer, modifier, supprimer + accès aux certificats';
echo '</fieldset>';

echo $this->Form->end(__('Create'));
