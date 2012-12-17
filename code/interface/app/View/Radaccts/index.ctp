<? 
$this->extend('/Common/radius_sidebar');
$this->assign('monitoring_active', 'active');

$columns = array(
	'acctuniqueid'			=> 'Session ID',
	'username'					=> 'Username',
	'callingstationid'	=> 'IP',
	'acctstarttime'			=> 'Start',
	'acctstoptime'			=> 'Stop',
	'nasipaddress'			=> 'NAS IP',
	'nasportid'					=> 'Nas Port',
);
?>
<h1>Sessions</h1>

<table class="table">
    <thead>
    <tr>
				<?
					foreach( $columns as $field => $text )
						echo "<th>" . $this->Paginator->sort($field, $text . " " . ( (preg_match( "#$field$#", $this->Paginator->sortKey()) ) ? $this->Html->tag('i', '', array('class' => $sortIcons[$this->Paginator->sortDir()])) : '' ), array( 'escape' => false )) . "</th>";
				?>
    </tr>
    </thead>

<? if(!empty($radaccts)){ ?>
    <tbody>
    <? foreach ($radaccts as $acct): ?>
    <tr>
        <td>
            <? echo $this->Html->link(	h($acct['Radacct']['acctuniqueid']),
            														array(	'controller' => 'Radaccts', 
																								'action' => 'view', 
																								$acct['Radacct']['radacctid']	)); ?>
        </td>
        <td>
            <? echo h($acct['Radacct']['username']); ?>
        </td>
        <td>
            <? echo h($acct['Radacct']['callingstationid']); ?>
        </td>
        <td>
            <? echo ( !empty( $acct['Radacct']['acctstarttime'] ) ) ? h($acct['Radacct']['acctstarttime']) : "Unknown"; ?>
        </td>
        <td>
            <? echo ( !empty( $acct['Radacct']['acctstoptime'] ) ) ? h($acct['Radacct']['acctstoptime']) : "Connected"; ?>
        </td>
        <td>
            <? echo h($acct['Radacct']['nasipaddress']); ?>
        </td>
        <td>
            <? echo ( !empty( $acct['Radacct']['nasportid'] ) ) ? h($acct['Radacct']['nasportid']) : "Unknown"; ?>
        </td>
    </tr>
    <? endforeach; ?>
    <? unset($acct); ?>
    </tbody>
<? }else{ ?>
    <tbody>
    <tr>
        <td colspan="7">
            No session found.
        </td>
    </tr>
    </tbody>
<? 
	}
?>
</table>

<? /* Version Bootstrap
<div class="pagination">
	<ul>
<?
	echo 	$this->Paginator->prev('Prev.', array('tag' => 'li'), '<a href="#">Prev.</a>', array( 'tag' => 'li', 'class' => 'disabled', 'escape' => false )) .
				$this->Paginator->numbers( array(	'tag' => 'li', 'currentTag' => 'strong', 'currentClass' => 'active', 'modulus' => 1, 'first' => 2, 'last' => 2, 'ellipsis' => '<li class="disabled"><a href="#">...</a></li>', 'separator' => '' )) .
				$this->Paginator->next('Next', array('tag' => 'li'), '<a href="#">Next</a>', array( 'tag' => 'li', 'class' => 'disabled', 'escape' => false ));
?>
	</ul>
</div>
*/
?>

<?
	$paginate = $this->Paginator->prev('Prev.', array(), null, array('class' => 'disabled')) .
							$this->Paginator->numbers( array(	'modulus'		=> 2,
																								'first'			=> 2,
																								'last'			=> 2,
																								'ellipsis'	=> "<span class='disabled'>...</span>",
																								'separator'	=> '',	
																								'currentClass' => 'disabled'
																								)) .
							$this->Paginator->next('Next', array(), null, array('class' => 'disabled'));

	echo $this->Html->tag('div', $paginate, array('class' => 'pagination pagination-small', 'style' => 'float:left;'));
?>

<div style="float:right;">
<?
	echo $this->Paginator->counter( array('format' => 'range') );
?>
</div>

