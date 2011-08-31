<?php include('inc/header.php'); ?>

<?php
// load Zend Gdata libraries
	require_once '/var/www/hotsheet/inc/js/ZendGdata/library/Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

// set API properties
    $user = "your_account_with_spreadsheet_access@yourdomain.com";
    $pass = "your_account_password";
   
// try to login and get our spreadsheet data but fail on error.
   try {	
	//Get the spreadsheet data
		$service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
		$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
		$service = new Zend_Gdata_Spreadsheets($client);
		$ssEntry = $service->getSpreadsheetEntry('https://spreadsheets.google.com/feeds/spreadsheets/your-spreasheet-key-goes-here');
		$wsFeed = $ssEntry->getWorksheets();	
		} catch (Exception $e) {
      echo '</pre>';
      echo '<p>WOOPS!</p> <p>Failed getting worksheet names.</p> <p>call IT!</p></div>';
      die('ERROR: ' . $e->getMessage());
    }
?>

<div id="main">	
	<h3>Please select an employee to view their hotsheet...</h3>
		<form id="form" method="get" action="status.php">
		<input type="hidden" name="sort_column" value="2" />
		<select id="sheet_id" name="sheet_id">
        <option label="Select..." value="">Select...</option>
		<?php 
		foreach($wsFeed as $wsEntry) {
		echo $wsEntry->getTitle();
		echo '<option label="'.$wsEntry->getTitle().'" value="'.$wsEntry->getTitle().'**'.substr($wsEntry->getID(), -3).'">'.$wsEntry->getTitle().'</option>';
		}
		?>
		<input type="submit" onClick="this.value='getting status...'" value="VIEW STATUS" id="submit" />
   </form>
</div> <!-- end of #main -->

<?php include('inc/footer.php'); ?>
