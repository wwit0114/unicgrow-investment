<?php
include('../../security_web_validation.php');

include("../function/functions.php");

$newp = $_GET['p'];
$plimit = "20";
$show_tab = 5;
if($newp == ''){ $newp='1'; }
$tstart = ($newp-1) * $plimit;
$tot_p = $plimit * $show_tab;

$qur_set_search = '';
if(count($_GET) == 1){
	unset($_SESSION['SESS_search_username'],$_SESSION['SESS_strt_date'],$_SESSION['SESS_en_date']);
}
else{
	$_POST['Search'] = '1';
	$_POST['search_username'] = $_SESSION['SESS_search_username'];
	$_POST['st_date'] = $_SESSION['SESS_strt_date'];
	$_POST['en_date'] = $_SESSION['SESS_end_date'];
}
if(isset($_POST['Search']))
{
	$_SESSION['SESS_search_username'] = $search_username = $_POST['search_username'];
	
	if($_POST['st_date'] != '' and $_POST['en_date'] != ''){
		$_SESSION['SESS_strt_date'] = $st_date = date('Y-m-d', strtotime($_POST['st_date']));
		$_SESSION['SESS_end_date'] = $en_date = date('Y-m-d', strtotime($_POST['en_date']));
	}
	

	$search_id = get_new_user_id($search_username);
	
	if($st_date !='' and $en_date != ''){
		$qur_set_search = " AND t1.date BETWEEN '$st_date' AND '$en_date' ";
	}
	if($search_username !=''){
		$qur_set_search = " AND t1.user_id = '$search_id' ";
	}
}
?>
<table class="table table-bordered">
	<tr>
		<td>
			<form action="index.php?page=excel_bonus" method="post">
				<input type="hidden" name="bonus_name" value="Binary Bonus" />
				<input type="hidden" name="inc_type" value="4" />
				<input type="hidden" name="url" value="<?=$val?>" />
				<input type="submit" name="Excel" value="Download Excel" class="btn btn-warning" />
			</form>
		</td>
		<form method="post" action="index.php?page=<?=$val?>">
		<td>
			<input type="text" name="search_username" placeholder="Search By Username" class="form-control" />
		</td>
		<td>
			<div class="form-group" id="data_1">
				<div class="input-group date">
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					<input type="text" name="st_date" placeholder="Start Date" class="form-control" />
				</div>
			</div>
		</td>
		<td>
			<div class="form-group" id="data_1">
				<div class="input-group date">
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					<input type="text" name="en_date" placeholder="End Date" class="form-control" />
				</div>
			</div>
		</td>
		<th><input type="submit" value="Search" name="Search" class="btn btn-info"></th>
		</form>
	</tr>
</table>

<?php
$sql = "SELECT t1.*,t2.username,t2.f_name,t2.l_name,t2.phone_no FROM income t1 
LEFT JOIN users t2 ON t1.user_id = t2.id_user
WHERE t1.type = 4 $qur_set_search ORDER BY t1.date DESC";	
$_SESSION['search_result'] = $sql;
$SQL = "$sql LIMIT $tstart,$tot_p ";

$sqlk = "SELECT SUM(amount) amt , COUNT(id) num FROM ($sql) t1";
$query = query_execute_sqli($sqlk);
while($ro = mysqli_fetch_array($query))
{
	$amount = $ro['amt'];
	$tot_rec = $ro['num'];
	$lpnums = ceil ($tot_rec/$plimit);
}

$query = query_execute_sqli($SQL);
$totalrows = mysqli_num_rows($query);
if($totalrows != 0)
{ ?>
	<table class="table table-bordered">
		<thead>
		<tr><th colspan="6">Total Binary Bonus : <?=round($amount,2); ?> &#36;</th></tr>
		<tr>
			<th class="text-center">Sr. No.</th>
			<th class="text-center">User ID</td>
			<th class="text-center">Name</td>
			<th class="text-center">Mobile No.</td>
			<th class="text-center">Total Bonus</td>
			<th class="text-center">Status</td>
			<!--<th class="text-center">TAX</th>
			<th class="text-center">TDS</th>
			<th class="text-center">Date</td>-->
		</tr>
		</thead>
		<?php
		$pnums = ceil($totalrows/$plimit);
		if($newp == ''){ $newp = '1'; }
			
		$start = ($newp-1) * $plimit;
		$starting_no = $start + 1;
		
		$sr_no = $starting_no;
		
		$que = query_execute_sqli("$sql LIMIT $start,$plimit");
		while($row = mysqli_fetch_array($que))
		{ 	
			$user_id = $row['user_id'];
			$date = date('d/m/Y' , strtotime($row['date']));
			$amount = $row['amount'];
			$mode = $row['mode'];
			$username = $row['username'];
			$level = $row['level'];
			$name = ucfirst($row['f_name'])." ".ucfirst($row['l_name']);
			
			$phone_no = $row['phone_no'];
			$tax = round($row['tax'],5);
			$tds_tax = round($row['tds_tax'],5); 
			
			//$cur_rank = get_user_current_rank($user_id);
			
			$tot_binary = get_user_which_type_bonus($user_id , 4);
			
			if($mode == 0)
			{
				$img = 'yes.png';
				$title = "Confirmed";
			}
			else
			{
				$img = 'close.png';
				$title = "Unconfirmed";
			} ?>
			<tr class="text-center">
				<td><?=$sr_no?></td>
				<td><?=$username;?></td>
				<td><?=$name?></td>
				<td><?=$phone_no?></td>
				<td>&#36; <?=round($tot_binary,2)?></td>
				<td>
					<form method="post" action="index.php?page=bonus_details" target="_blank">
						<input type="hidden" name="user_id" value="<?=$user_id?>" />
						<input type="hidden" name="username" value="<?=$username?>" />
						<input type="hidden" name="type" value="4" />
						<input type="submit" name="status" value="Status" class="btn btn-success btn-xs" />
					</form>
				</td>
				<!--<td>&#36; <?=$tax?></td>
				<td>&#36; <?=$tds_tax?></td>
				<td><?=$date?></td>-->
				<!--<td><?=$cur_rank?></td>
				<td><img src="../images/<?=$img;?>" title="<?=$title?>" /></td>-->
			</tr> <?php
			$sr_no++;
		} ?>
	</table> <?PHP
	pagging_initation_last_five_admin($newp,$lpnums,$show_tab,$val);
}
else{ echo "<B class='text-danger'>There are no information to show!</B>";  }
?>