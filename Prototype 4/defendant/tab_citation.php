<?
$citation = new Citation( $defendant->getDefendantID() );
?>

<div id="officer-dialog" title="Add New Officer">
  <form id="officer">
    <input type="hidden" name="action" value="Add Officer" />
    <table>
      <tr>
        <td width="100">Identification:</td>
        <td><input type="text" name="officer-idNumber" id="officer-idNumber" /></td>
      </tr>
      <tr>
        <td>Last Name:</td>
        <td><input type="text" name="officer-lastname" id="officer-lastname" /></td>
      </tr>
      <tr>
        <td>First Name:</td>
        <td><input type="text" name="officer-firstname" id="officer-firstname" /></td>
      </tr>
      <tr>
        <td>Phone Number:</td>
        <td><input type="text" name="officer-phone" id="officer-phone" /></td>
      </tr>
    </table>		
  </form>
</div>

<div id="common-location-dialog" title="Add New Common Location">
  <form id="common-location-form">
    <input type="hidden" name="action" value="Add Common Location" />
    <label>Common Location</label>
    <input type="text" name="common-location-name" id="common-location-name" style="width: 275px;" />
  </form>
</div>

<div id="offense-existing-dialog" title="Add Existing Offense">
  <table id="statute-table">
    <thead>
        <tr>
        	<th>StatuteID</th>
          <th>Statute</th>
          <th align="left">Description</th>
        </tr>
    </thead>
    <tbody></tbody>
  </table> 
</div>

<div id="offense-new-statute" title="Add New Statute">
  <form id="statute" action="process.php" method="post">
    <input type="hidden" name="action" value="Add Statute" />
  	<input type="hidden" name="defendantID" value="<? echo $defendant->getDefendantID() ?>" />
     <table>
      <tr>
        <td width="100">Code:</td>
        <td><input type="text" name="statute-code"  id="statute-code" style="width: 300px" /></td>
      </tr>
      <tr>
        <td>Title:</td>
        <td><input type="text" name="statute-title" id="statute-title" style="width: 300px" /></td>
      </tr>
      <tr>
        <td valign="top">Description:</td>
        <td><textarea name="statute-description" id="statute-description" style="width: 300px; height: 100px;"></textarea></td>
      </tr>
    </table>
  </form>
</div>

<div id="item-dialog" title="Add Stolen/Vandalized Item">
  <form id="item" action="process.php" method="post">
    <input type="hidden" name="action" value="Add Stolen Item" />
  	<input type="hidden" name="defendantID" value="<? echo $defendant->getDefendantID() ?>" />
    <table>
      <tr>
        <td width="30">Item:</td>
        <td><input type="text" name="item-name" size="30"></td>
      </tr>
      <tr>
        <td>Value:</td>
        <td><input type="text" name="item-value" size="10"></td>
      </tr>
    </table>
  </form>
</div>

<div id="vehicle-dialog" title="Add Vehicle">
  <form id="vehicle" action="process.php" method="post">
    <input type="hidden" name="action" value="Add Vehicle" />
    <input type="hidden" name="defendantID" value="<? echo $defendant->getDefendantID() ?>" />
    <table>
      <tr>
        <td width="50">Year:</td>
        <td><input type="text" name="vehicle-year" id="vehicle-year" ></td>
      </tr>					
      <tr>
        <td>Make:</td>
        <td><input type="text" name="vehicle-make" id="vehicle-make" ></td>
      </tr>	
      <tr>
        <td>Model:</td>
        <td><input type="text" name="vehicle-model" id="vehicle-model" ></td>
      </tr>	
      <tr>
        <td>Color:</td>
        <td><input type="text" name="vehicle-color" id="vehicle-color" ></td>
      </tr>	
      <tr>
        <td>License:</td>
        <td><input type="text" name="vehicle-license" id="vehicle-license" ></td>
      </tr>	
      <tr>
        <td>State:</td>
        <td><input type="text" name="vehicle-state" id="vehicle-state" ></td>
      </tr>	
      <tr>
        <td valign="top">Comment:</td>
        <td><textarea name="vehicle-comment" id="vehicle-comment" style="width: 300px; height: 75px;"></textarea></td>
      </tr>	
    </table>
	</form>
</div>

<?
$citation = new Citation( $defendant->getDefendantID() );
?>
<form name="citation" id="citation" action="process.php" method="post">
  <input type="hidden" name="action" value="Update Citation" />
  <input type="hidden" name="defendantID" value="<? echo $defendant->getDefendantID() ?>" />
  <fieldset>
    <legend>Citation Infomation</legend>
    <table>
      <tr>
        <td>Date:</td>
        <td><input type="text" name="citation-date" id="citation-date" value="<? echo date("m/d/Y", $citation->citationDate); ?>" /></td>
        <td>Officer:</td>
        <td>
        	<? $data = new Data(); ?>
          <select name="officerID" id="officerID">
          	<option></option>
            <? echo $data->fetchOfficerDropdown( $user_programID, $citation->officerID )?>
          </select>
          <a id="add-officer" style="cursor:pointer;"><img src="/images/add.png" border="0" align="absmiddle" /></a>
        </td>
      </tr>
      <tr>
        <td>Time:</td>
        <td><input type="text" name="citation-time" id="citation-time" value="<? echo date("h:i A", $citation->citationDate); ?>" /></td>
        <td>Miranda Given?</td>
        <td>
          <select name="miranda">
            <option value="1"<? if( $citation->mirandized == 1 ) echo " selected"; ?>>Yes</option>
            <option value="0"<? if( $citation->mirandized == 0 ) echo " selected"; ?>>No</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Address:</td>
        <td><input type="text" name="citation-address" style="width: 300px;" value="<? echo $defendant->address ?>"/></td>
        <td>Drugs/Alcohol?</td>
        <td>
          <select name="drugs-alcohol">
            <option value="1"<? if( $citation->drugsOrAlcohol == 1 ) echo " selected"; ?>>Yes</option>
            <option value="0"<? if( $citation->drugsOrAlcohol == 0 ) echo " selected"; ?>>No</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Common Place:</td>
        <td>
          <select style="width: 310px;" name="common-location" id="common-location">
            <option></option>
            <? echo $data->fetchCommonLocationDropdown( $user_programID, $citation->commonLocationID )?>
          </select>
          <a id="add-common-location" style="cursor:pointer;"><img src="/images/add.png" border="0" align="absmiddle" /></a>
          <? unset( $data ); ?>
        </td>
      </tr>
      <tr>
        <td>City:</td>
        <td>
          <? 
          $location = new Location( $defendant->getProgramID() );
          $location->getFromID( $citation->locationID );
          ?>
          <input type="text" name="citation-city" id="citation-city" value="<? echo $location->city ?>" />
          State: <input type="text" name="citation-state" id="citation-state" size="2" value="<? echo $location->state ?>" />
          Zip: <input type="text" name="citation-zip" id="citation-zip" size="7" value="<? echo $location->zip ?>" />
          <a class="select-location" id="citation-location" style="cursor:pointer;"><img src="/images/add.png" border="0" align="absmiddle" /></a>
          <?
          unset($location);
          ?>
        </td>
        <td colspan="2" align="right"><button id="citation-submit">Update Citation Information</button></td>
      </tr>
    </table>
  </fieldset>
</form>

<fieldset>
    <legend>Offense</legend>					
    <table class="listing" id="offense-listing">
      <thead>
        <tr>
        	<th></th>
          <th style="text-align: center;">Statue</th>
          <th>Description</th>
          <th></th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <div class="belowListing">
    <button id="add-existing-offense">Add Existing Offense</button>
    <button id="add-new-statute">Add New Statute</button>
    </div>
</fieldset>

<fieldset>
    <legend>Stolen/Vandalized Items</legend>
    <table class="listing" id="stolen-listing">
      <thead>
        <tr>
          <th width="75%">Item</th>
          <th width="20%">Value</th>
          <th width="5%"></th>
        </tr>
      </thead>
      <tbody>
      <?
			$items = $citation->getStolenItems();
			if( count($items) == 0 ) {
				echo '<tr><td align="center" colspan="3">No items to display</td></tr>';
			} else {
				foreach( $items as $row )  { 
					echo '<tr>';
					echo '<td>'.$row["name"].'</td>';
					echo '<td>'.$row["value"].'</td>';
					echo '<td><a class="delete-item" href="process.php?action=Delete Stolen Item&defendantID='.$defendant->getDefendantID().
							 '&itemID='.$row["itemID"].'">Delete</a></td>';
					echo '</tr>';
				}
			}
			?>
      </tbody>
    </table>
    <button id="add-item">Add Item</button>
</fieldset>

<fieldset>
    <legend>Vehicles Involved</legend>
    <table class="listing" id="vehicle-listing">
      <thead>
        <tr>
          <th width="10%">Year</th>
          <th width="20%">Make</th>
          <th width="20%">Model</th>
          <th width="20%">Color</th>
          <th width="15%">License</th>
          <th width="10%">State</th>
          <th width="5%"></th>
        </tr>
      </thead>
      <tbody>
      <?
			$vehicles = $citation->getVehicles();
			if( count($vehicles) == 0 ) {
				echo '<tr><td align="center" colspan="7">No vehicles to display</td></tr>';
			} else {
				foreach( $vehicles as $row )  { 
					echo '<tr>';
					echo '<td>'.$row["year"].'</td>';
					echo '<td>'.$row["make"].'</td>';
					echo '<td>'.$row["model"].'</td>';
					echo '<td>'.$row["color"].'</td>';
					echo '<td>'.$row["license"].'</td>';
					echo '<td>'.$row["state"].'</td>';
					echo '<td><a class="delete-vehicle" href="process.php?action=Delete Vehicle&defendantID='.$defendant->getDefendantID().
							 '&vehicleID='.$row["vehicleID"].'">Delete</a></td>';
					echo '</tr>';
					
					if( $row["comment"] ) {
						echo '<tr><td></td>';
						echo '<td colspan="6"><strong>Comments:</strong> '.$row["comment"].'</td>';
						echo '</tr>';
					}
					
				}
			}
			?>
      </tbody>
    </table>
    <div>
      <button id="add-vehicle">Add Vehicle</button>
    </div>
</fieldset>
<? unset( $citation ); ?>
