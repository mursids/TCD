<?php
$menuarea = "court";
include($_SERVER['DOCUMENT_ROOT']."/includes/header_internal.php");
?>

<script src="/includes/js/dataTables.ajaxReload.js"></script>
<script>
jQuery(function($)
{
	// if tab cookie is set, reset it to 0
	$.removeCookie('ui-tabs-1');
	
	// build data table
	var oTable = $('#data-table').dataTable( {
		"aaSorting": [],
		"sPaginationType": "full_numbers",
		"sAjaxSource": '/data/courts.php?type=time',
		"fnServerData": function ( sSource, aoData, fnCallback ) {
				// use ajax to get the source data
				$.ajax( {
						"dataType": 'json',
						"type": "GET",
						"url": sSource,
						"cache": false,
						"data": aoData,
						"success": fnCallback
    	});
  	}
  });

	// refresh the dataTable every 10 seconds
  var newtimer = setInterval( function() { oTable.fnReloadAjax(); }, 10000 );
});
</script>

<h1>Courts Needing Time Entered</h1>

<table id="data-table">
	<thead>
		<tr>
      <th width="125">Type</th>
      <th width="125">Date</th>
      <th width="150">Place</th>
      <th width="150">Location</th>
      <th width="50"></th>
		</tr>
	</thead>
	<tbody></tbody>
</table>

<?php
include($_SERVER['DOCUMENT_ROOT']."/includes/footer_internal.php");
?>
