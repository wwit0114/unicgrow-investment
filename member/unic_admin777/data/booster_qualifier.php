<?php
include('../../security_web_validation.php');

include("../function/functions.php");

$newp = $_GET['p'];
$plimit = "20";
$show_tab = 5;
if($newp == ''){ $newp='1'; }
$tstart = ($newp-1) * $plimit;
$tot_p = $plimit * $show_tab;
?>

<form method="post" action="index.php?page=booster_qualifier">
<table class="table table-bordered">
	<tr>
		<td><input type="text" name="search_username" placeholder="Search By Username" class="form-control" /></td>
		<td>
			<div class="form-group" id="data_1">
				<div class="input-group date">
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					<input type="text" name="date" placeholder="Search By Date" class="form-control" />
				</div>
			</div>
		</td>
		<td><input type="submit" value="Search" name="Search" class="btn btn-info"></td>
	</tr>
</table>
</form>	
<?php

$qur_set_search = '';
if(count($_GET) == 1){
	unset($_SESSION['SESS_search_username'],$_SESSION['SESS_search_date']);
}
else{
	$_POST['Search'] = '1';
	$_POST['search_username'] = $_SESSION['SESS_search_username'];
	$_POST['date'] = $_SESSION['SESS_search_date'];
}
if(isset($_POST['Search']))
{
	if($_POST['date'] != '')
	$_SESSION['SESS_search_date'] = $date = date('Y-m-d', strtotime($_POST['date']));
	$_SESSION['SESS_search_username'] = $search_username = $_POST['search_username'];	
	
	$search_id = get_new_user_id($search_username);
	
	if($date !=''){
		$qur_set_search = " AND t1.date = '$date' ";
	}
	if($search_username !=''){
		$qur_set_search = " AND t1.user_id = '$search_id' ";
	}
}

/*if(isset($_POST['create_file']))
{
	$file_name = time()."Wallet Info".date('Y-m-d');
	$sep = "\t";
	$fp = fopen('mlm_user excel files/'.$file_name.'.xls', "w");
	$insert = ""; 
	$insert_rows = ""; 
	
	$SQL = "SELECT t1.*,t2.username,t2.f_name,t2.l_name FROM wallet t1 
	LEFT JOIN users t2 ON t1.id = t2.id_user
	$qur_set_search";	
	$result = query_execute_sqli($SQL);              
	
	$insert_rows.="Username \t Name \t Bonus Wallet \t Company Wallet \t Activation Wallet \t Date";
	$insert_rows.="\n";
	fwrite($fp, $insert_rows);
	while($row = mysqli_fetch_array($result))
	{
		$insert = "";
		$username = $row['username'];
		$amount = $row['amount'];
		$companyw = $row['companyw'];
		$activationw = $row['activationw'];
		$date = date('d/m/Y' , strtotime($row['date']));
		$name = ucfirst($row['f_name'])." ".ucfirst($row['l_name']);
		
		$insert .= $username.$sep;
		$insert .= $name.$sep;
		$insert .= $amount.$sep;
		$insert .= $companyw.$sep;
		$insert .= $activationw.$sep;
		$insert .= $date.$sep;
		
		$insert = str_replace($sep."$", "", $insert);
		
		$insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $insert);
		$insert .= "\n";
		fwrite($fp, $insert);
	}
	fclose($fp);
	echo "<B style='color:#008000;'>Excel File Created Successfully !</B>";
	?>
	<p><a style="color:#333368; font-weight:600;" href="index.php?page=<?=$val?>">Back</a></p>
	click here for download file = <a href="mlm_user excel files/<?=$file_name;?>.xls"><?=$file_name; ?></a> <?php 
}
else
{*/


$sql = "SELECT t1.*,t2.username,t2.f_name,t2.l_name,t2.email FROM reg_fees_structure t1 
LEFT JOIN users t2 ON t1.user_id = t2.id_user
WHERE t1.boost_id > 0
$qur_set_search ORDER BY t1.id DESC";
$SQL = "$sql LIMIT $tstart,$tot_p ";

$query = query_execute_sqli($SQL);
$totalrows = mysqli_num_rows($query);

$sqlk = "SELECT COUNT(t1.id) num FROM reg_fees_structure t1 WHERE t1.boost_id > 0 $qur_set_search";
$query = query_execute_sqli($sqlk);
while($ro = mysqli_fetch_array($query))
{
	$tot_rec = $ro['num'];
	$lpnums = ceil ($tot_rec/$plimit);
}

if($totalrows != 0)
{ ?>
	<table class="table table-bordered">
		<thead>
		<tr>
			<th class="text-center">Sr. No.</th>
			<th class="text-center">Username</th>
			<th class="text-center">Name</th>
			<th class="text-center">E-mail</th>
			<th class="text-center">Date</th>
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
			$date = date('d/m/Y' , strtotime($row['date']));
			$email = $row['email'];
			$username = $row['username'];
			$name = ucfirst($row['f_name'])." ".ucfirst($row['l_name']);
			?>
			<tr class="text-center">
				<td><?=$sr_no?></td>
				<td><?=$username;?></td>
				<td><?=$name?></td>
				<td><?=$email?></td>
				<td><?=$date?></td>
			</tr> <?php
			$sr_no++;
		} ?>
	</table> <?php
	pagging_initation_last_five_admin($newp,$lpnums,$show_tab,$val);
}
else{ echo "<B class='text-danger'>There are no information to show !!</B>"; }	
//}
?>