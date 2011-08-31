<?php include('inc/header.php'); ?>


<?php
//Check to see if our post returned any data, otherwise fail.
if ( $_REQUEST && $_REQUEST['sheet_id'])
{ 
$sheet_name = strstr($_REQUEST['sheet_id'], '**', 1);
$sheet_id = strstr($_REQUEST['sheet_id'], '**');
$sheet_id = str_replace('**', '', $sheet_id);
$sort_column  = $_REQUEST['sort_column'];


	// load Zend Gdata libraries
	require_once '/var/www/hotsheet/inc/js/ZendGdata/library/Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

	// set API properties
	$user = "your_account_with_spreadsheet_access@yourdomain.com";
    $pass = "your_account_password";
	$spreadsheetService = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
	$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $spreadsheetService);
	$spreadsheetService = new Zend_Gdata_Spreadsheets($client);
	$spreadsheetKey = 'your-spreasheet-key-goes-here';
    
    try {  
      // Here we try to get the column names from our spreadsheet.
      $header_query = new Zend_Gdata_Spreadsheets_CellQuery();
      $header_query->setSpreadsheetKey($spreadsheetKey);
      $header_query->setWorksheetId($sheet_id);
      $header_query->setMinRow(1);
      $header_query->setMaxRow(1);
      $header_feed = $spreadsheetService->getCellFeed($header_query);
    } catch (Exception $e) {
      echo '<div id="main"><div id="status"><h3>WOOPS!, there has been an error.</h3>';
      die('<h3>'.$e->getMessage().'</h3><h3>Please <a href="http://www.yourdomain.com/">try again</a> or <a href="mailto:support@yourdomain.com">contact IT for support</a> and provide the error above.</h3></div></div>');
    }
    
    echo $sort_column_name;
    // Here we try to get the data in our spreadhseet
    try {	
		$data_query = new Zend_Gdata_Spreadsheets_ListQuery();
		$data_query->setSpreadsheetKey($spreadsheetKey);
		$data_query->setWorksheetId($sheet_id);
		$sort_column_name = (strtolower ($header_feed[$sort_column]->getCell()->getText()));
		$data_query->setOrderBy('column:'.$sort_column_name);
		$data_feed = $spreadsheetService->getListFeed($data_query);
	} catch (Exception $e) {
      echo '<div id="main"><div id="status"><h3>WOOPS!, there has been an error.</h3>';
      die('<h3>'.$e->getMessage().'</h3><h3>Please <a href="http://www.yourdomain.com/">try again</a> or <a href="mailto:support@yourdomain.com">contact IT for support</a> and provide the error above.</h3></div></div>');
    }
?>

<div id="main">	
		<div id="status">
		<?php
		echo '<h3>hotsheet for '.$sheet_name.'</h3>';
		$count = 1;
		?>
		<table id="status-table">
		<thead>  
            <tr> 
            	<?php
            	$column_count = 0;
            	foreach ($header_feed as $header_item) {
        		echo '<th scope="col" id="col1">';
        		echo '<a href="status.php?sheet_id='.$sheet_name.'**'.$sheet_id.'&sort_column='.$column_count.'">'.$header_item->getCell()->getText().'</a>';
        		echo '</th>';
      			$column_count++;
      			}
      			?>
            </tr>  
        </thead> 
        <tbody>
        <?
		foreach ($data_feed as $data_entry) {
			echo '<tr>';
			$data_row = $data_entry->getCustom();			
			//Here we step through the new sorted array and print our results.
			foreach($data_row as $this_data_row) {
				echo '<td>';
				echo $this_data_row->getText();
				echo '</td>';
			}
			echo '</tr> ';
			$count++;
		}
		?>
		 </tbody>
		  <tfoot>  
            <tr>  
                <td><? echo $count.' tasks returned'; ?></td> 
                <td>
               
                </td>
            </tr>  
        </tfoot> 
	</table>  
	</div> <!-- end of #status -->
</div> <!-- end of #main -->
  
<?php 
} else {
    echo 'You did not select a user to view status for. <a href="http://www.yourdomain.com/">Please Try Again.</a>';
}
?>

<?php include('inc/footer.php'); ?>
