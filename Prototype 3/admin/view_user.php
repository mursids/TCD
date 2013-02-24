<?php
$menuarea = "admin";
include($_SERVER['DOCUMENT_ROOT']."/includes/header_internal.php");
include($_SERVER['DOCUMENT_ROOT']."/includes/class_data.php");

$data = new Data();
$id = $_GET["id"];

if( isset($id) ) {
	$action = "Edit User";
	
	$user_mod = new User();
	$user_mod->getFromID( $id );
	$firstname = $user_mod->getFirstName();
	$lastname = $user_mod->getLastName();
	$email = $user_mod->getEmail();
	$active = $user_mod->isActive();
	$type = $user_mod->getType();
	$programID = $user_mod->getProgramID();
	$timezoneID = $user_mod->getTimezoneID();
	
	// make sure logged in user has access to edit this user
	if( $user_type > 2 && $programID != $program->getProgramID() )
	{
		echo "NO ACCESS!";
	}

} else {
	$action = "Add User";
	$firstname = "";
	$lastname = "";
	$email = "";
	$active = 0;
	$type = 5;
	$programID = 0;
	$timezoneID = 1;
}
?>

<script>
$(function () {
	
	$( "#user-list" ).button().click(function() {	window.location.href = "users.php";	});	
	$( "#add-user" ).button().click(function() {	$("#newUser").submit();	});	
	$( "#update-user" ).button().click(function() {	$("#newUser").submit();	});
	$( "#delete-user" ).button().click(function() {	 });
	
	$("#newUser").validate({
		errorElement: "div",
		wrapper: "div",
		errorPlacement: function(error, element) {
			  error.insertAfter(element);
				error.addClass('message');
		},
		rules: {
			email: {
				required: true,
				email: true
			},
			firstname: {
				required: true
			}
			<? if( $action == "Add") { ?>
			, password: {
				required: true
			}
			<? } ?>
			
		}
	});
	
});
</script>



<div id="control-header">
	<div class="left"><h1><? echo $action ?></h1></div>	
	<div class="right">
		<div id="control" class="ui-state-error">
			<button id="user-list">Back to List</button>
			<? if( $action == "Add User" ) { ?>
			<button id="add-user">Add User</button>
			<? } else { ?>
			<button id="update-user">Update User</button>
			<button id="delete-user">Delete User</button>
      <? } ?>
		</div>
	</div>
</div>

<form name="newUser" id="newUser" method="post" action="process.php">
<input type="hidden" name="action" value="<? echo $action ?>" />
<input type="hidden" name="userID" value="<? echo $id ?>" />

<fieldset>
  <legend>User Information</legend>
  <table>
    <tr>
      <td width="200">Active:</td>
      <td>
        <select name="active">
          <option value="1"<? if($active == 1) echo " selected"; ?>>Yes</option>
          <option value="0"<? if($active == 0) echo " selected"; ?>>No</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>First Name:</td>
      <td><input type="text" name="firstname" value="<? echo $firstname ?>" /></td>
    <tr>
      <td>Last Name:</td>
      <td><input type="text" name="lastname" value="<? echo $lastname ?>" /></td>
    </tr>
    <tr>
      <td>Email Address:</td>
      <td><input type="text" name="email" value="<? echo $email ?>" /></td>
    </tr>
    <tr>
      <td>Force Password:</td>
      <td><input type="password" name="password"/></td>
    </tr>
  </table>
</fieldset>
    
<fieldset>
  <legend>Program Information</legend>
  <table>
    <tr>
      <td width="200">Program:</td>
      <td>
        <? 
        if( $user_type > 2 ) { 
            echo $program->getName();
        } else {
        ?>
        <select name="programID">
          <? echo $data->fetchProgramDropdown( $programID ); ?>
        </select>
        <? } ?>
      </td>
    </tr>
    <tr>
      <td>Access Level:</td>
      <td>
        <select name="typeID">
          <? echo $data->fetchUserTypeDropdown( $type, $user_type); ?>
        </select>
      </td>
    </tr>
    <tr>
      <td>Timezone:</td>
      <td>
        <select name="timezoneID">
          <? echo $data->fetchTimezoneDropdown( $timezoneID ); ?>
        </select>
      </td>
    </tr>
  </table>
</fieldset>

</form>

<?php 
include($_SERVER['DOCUMENT_ROOT']."/includes/footer_internal.php");
?>