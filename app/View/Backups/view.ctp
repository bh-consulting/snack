<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');
?>

<h1><?php echo __('View'); ?></h1>

<?php
if (isset($current) && isset($nas)) {
?>
<h2><?php echo __('Context'); ?></h2>

<dl class="well dl-horizontal">
    <dt><?php echo __('NAS'); ?></dt>
    <dd>
<?php
    echo $this->Html->link(
        "<i class='glyphicon glyphicon-hdd'></i> {$nas['Nas']['shortname']}",
		array(
		    'controller' => 'nas',
		    'action' => 'view',
		    $nas['Nas']['id'],
		),
		array('escape' => false)
    ) . ' (' . $nas['Nas']['nasname'] . ')';
?>
    </dd>
    <dt><?php echo __('When'); ?></dt>
    <dd><?php echo $current['Backup']['datetime'] ?></dd>
    <dt><?php echo __('Who'); ?></dt>
    <dd>
<?php
    echo $this->element(
        'formatUsersList',
        array('users' => $current['Backup']['users'])
    );
?>
    </dd>
    <dt><?php echo __('Why'); ?></dt>
    <dd><?php echo $current['Backup']['action'] ?></dd>
</dl>
<?php
    if (empty($diff)) {
?>
    <h4>
        <i class="glyphicon glyphicon-ok-sign glyphicon glyphicon-green" style="margin-top:4px;"></i>&nbsp;
        <?php echo __('This is the current configuration.') ?>
    </h4>
<?php
    }
?>
</dl>
<?php
}
?>

<?php
if (!empty($diff)) {
?>

<?php
}

if (!empty($content)) {
?>
<h2><?php echo __('Contents'); ?></h2>

<div class="toggleBlock" onclick="toggleBlock(this)">
    <?php echo $this->Html->link(__('Show'), '#') ?>
    <i class="glyphicon glyphicon-chevron-down"></i>
</div>

<div style="display:none">
    <pre class="well"><?php echo trim($content) ?></pre>
<?php
    /*if (!empty($diff) && !empty($current) && !empty($nas)) {
        echo '<div id="modaldel">';
        echo $this->element('modalDelete', array(
            'id'   => 'restore',
            'title' => __('Restore'),
            'link' => $this->Html->link(
                '<i class="glyphicon glyphicon-refresh glyphicon glyphicon-white"></i> ' . __('Restore'),
                array(
                    'controller' => 'backups',
                    'action' => 'restore',
                    $nas['Nas']['id'],
                    $current['Backup']['id'],
                ),
                array(
                    'escape' => false,
                    'class'  => 'btn btn-primary'
                )
            )
        ));
        echo '</div>';
        echo $this->Html->link(
            '<i class="glyphicon glyphicon-repeat glyphicon glyphicon-white"></i> ' . __('Restore'),
            '#confirmrestore',
            array(
                'data-toggle' => 'modal',
                'escape' => false,
                'class' => 'btn btn-primary',
            )
        );
    }*/
}
?>
</div>

<?php
$columns = array(
    'id' => __('ID'),
    'datetime' => __('Date'),
    'action' => __('Action'),
    'users' => __('Users'),
);
?>
<h2><?php echo __('Newer similar backups'); ?></h2>

<table class="table tableBackups">
    <thead>
        <tr>
<?php
foreach ($columns as $field => $text) {
    $sort = '';

    if (preg_match("#$field$#", $this->Paginator->sortKey())) {
        $sort = '<i class="' . $sortIcons[$this->Paginator->sortDir()] . '"></i>';
    }

    echo '<th '.($field == 'id' ? 'class="smallCol fit"' : '').'>'
        . $this->Paginator->sort($field, "$text $sort", array('escape' => false))
        . '</th>';
}
?>
        </tr>
    </thead>

    <tbody>
<?php
if (count($similar) > 1) {
    foreach ($similar as $backup) {
        echo '<tr>';

        foreach ($columns as $field => $text) {
            if ($field == 'id')
                echo '<td class="smallCol fit" style="font-weight: bold">';
            else if ($backup['Backup']['id'] == $current['Backup']['id'])
                echo '<td style="font-weight: bold">';
            else
                echo '<td style="font-style: italic">';

            if ($field == 'users') {
                echo $this->element('formatUsersList', array(
                    'users' => $users[$backup['Backup']['id']]
                ));
            } else {
                echo $backup['Backup'][$field];
            }

            echo '</td>';
        }

        echo '</tr>';
    }
} else {
?>
        <tr>
            <td colspan="5" style="text-align:center;">
                <?php echo __('No similar backups found'); ?>
            </td>
        </tr>
<?php
}
?>
    </tbody>
</table>

<?php
echo $this->element('paginator_footer');
?>

<br />
<br />
<br />

<?php
echo $this->Html->link(
    '<i class="glyphicon glyphicon-arrow-left glyphicon glyphicon-white"></i> '
    . '<i class="glyphicon glyphicon-camera glyphicon glyphicon-white"></i> '
    . __('Go back to backups'),
        array(
            'controller' => 'backups',
            'action' => 'index',
            $nas['Nas']['id'],
        ),
    array(
        'escape' => false,
        'class' => 'btn btn-primary',
    )
);
?>
