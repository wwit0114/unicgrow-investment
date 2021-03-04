<?php
include('../security_web_validation.php');
?>
<?php
include("condition.php");
include("function/setting.php");
include("function/send_mail.php");

include("function/wallet_message.php");
?>
<script>$(document).ready(function() {	
	$("#sponsor_username").keyup(function (e) {
		//removes spaces from username
		$(this).val($(this).val().replace(/\s/g, ''));
		var sponsor_username = $(this).val();
		if(sponsor_username.length < 5){$("#user-result").html('');return;}
		
		if(sponsor_username.length >= 5){
			$("#user-result").html('<img src="img/ajax-loader.gif" />');
			$.post('check_username.php', {'sponsor_username':sponsor_username},function(data)
			{
			  $("#user-result").html(data);
			});
		}
	});	
});		
</script>
<?php

$id = $_SESSION['mlmproject_user_id'];
$position = $_SESSION['position'];
if(isset($_POST['submit']))
{
	//$user_pin = $_REQUEST['user_pin'];
	$pin_no = $_REQUEST['pin_no'];
	$request_pin = $_REQUEST['request_pin'];
	$requested_user = $_REQUEST['requested_user'];
	$requested_user_id = get_new_user_id($requested_user);
	$request_date = $systems_date;
	
	if($requested_user_id == 0){ echo "<B class='text-danger'>Please Enter correct Username !</B>";	}
	else
	{	
		if($id == $requested_user_id){ echo "<B class='text-danger'>Please Transfer To Another Member !</B>"; }
		else
		{
			$left_amount = $current_amount-$request_amount;
			$query = query_execute_sqli("SELECT * FROM e_pin WHERE user_id = '$id' AND mode = 2 LIMIT $pin_no");
			$pin_num = mysqli_num_rows($query);
			if($pin_num > 0)
			{
				if($pin_num >= $pin_no)
				{
					while($row = mysqli_fetch_array($query))
					{
						$epin_id = $row['id'];
						
						$SQL = "UPDATE e_pin SET user_id = '$requested_user_id' , date = '$request_date' 
						WHERE id = '$epin_id' ";
						query_execute_sqli($SQL);
						
						$qus = "SELECT * FROM e_pin t1 
						INNER JOIN epin_history t2 ON t1.id = t2.epin_id AND t1.user_id = '$id' AND t1.mode = 2 
						LIMIT $pin_no ";
						$query = query_execute_sqli($qus);
						while($rok = mysqli_fetch_array($query))
						{
							$epin_new_id = $rok['id'];
							$generate_id = $rok['generate_id'];
							$transfer_to = $rok['transfer_to'];
						}
						$sql = "INSERT INTO epin_history (epin_id, generate_id , user_id ,transfer_to, date) 
						VALUES ('$epin_id', '$generate_id', '$transfer_to', '$requested_user_id', '$request_date')";
						query_execute_sqli($sql);
					}
					$message = "<B class='text-success'>$pin_no Registration E-pin Successfully Transfer To $requested_user</B>";
					$phone = get_user_phone($id);
					send_sms($phone,$message);
					
					echo "<B class='text-success'>You request of transfer E-pin ".$request_pin." has completed successfully!</B>";
				}
				else{ echo "<B class='text-danger'>You Can Transfer Only $pin_num E-pin !</B>"; }
			}	
			else{ echo "<B class='text-danger'>Please Enter Correct Number to Transfer !</B>"; }
		}	
	}		
}
else
{
	$query = query_execute_sqli("SELECT * FROM e_pin WHERE user_id = '$id' AND mode = 2 ");
	$num = mysqli_num_rows($query);
	if($num != 0)
	{ ?> 
		<form name="money" action="" method="post">
			<table class="table table-bordered table-hover">
			<thead><tr><th colspan="2">Your Epin Information </th></tr></thead>
				<tr><th colspan="2" class="td_title">Your Total Registraion Pin is : <?=$num; ?></th></tr>
				<tr>
					<td class="td_title">No of E-pin :</td>
					<td><input type="text" name="pin_no" class="form-control" /></td>
				</tr>
				<tr>
					<td class="td_title">Requested Username :</td>
					<td>
						<input type="text" name="requested_user" class="form-control" id="sponsor_username" />
						<span id="user-result"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="text-center">
						<input type="submit" name="submit" value="Request" class="btn btn-primary" />
					</td>   
				</tr>
			</table>
		</form> <?php 
	}
	else{ echo "<B class='text-danger'>You Have No Unused Epin to transfer !</B>"; }
}  ?>
