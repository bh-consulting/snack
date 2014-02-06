<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo __('Please Sign In'); ?></h3>
            </div>
            <div class="panel-body">

                <?php echo $this->Session->flash('auth'); ?>
                <?php echo $this->Form->create('Raduser'); ?>

                <fieldset>
                    <legend>
                    </legend>
                    <?
                    echo $this->Form->input(
                    'username',
                    array(
                    'placeholder' => __('Username'),
                    'label' => false,
                    'class' => 'form-control',
                    'id' => 'username_login',
                    'autocomplete' => 'off',
                    )
                    );
                    echo $this->Form->input(
                    'passwd',
                    array(
                    'placeholder' => __('Password'),
                    'label' => false,
                    'class' => 'form-control',
                    'id' => 'password_login',
                    'autocomplete' => 'off',
                    )
                    );
                    ?>
                </fieldset>
                <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __('Sign in'); ?></button>
                <? echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->start('script'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#username_field').focus();
    });
</script>
<?php $this->end(); ?>
