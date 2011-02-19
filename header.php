<?php

	session_start();
	
	$mysql_database = 'trade';
	$mysql_user = 'gimeo';
	$mysql_password = 'password';
	$mysql_location = 'localhost';
	$mysql_prefix = '';
	
	$mysql = mysql_connect($mysql_location, $mysql_user, $mysql_password) 
		or die('Could not connect to mySQL: ' . mysql_error());
	mysql_select_db($mysql_prefix . $mysql_database, $mysql) or die("mysql error: " . mysql_error());
	
	$starting_cash = 1000;	# Dollars
	
	// Game Mode
	if (isset($_GET['mode'])) {
		if ($_GET['mode'] == 'materials' or $_GET['mode'] == 'computers') {
			session_destroy();
			define("GAME_MODE", $_GET['mode']);
			session_start();
		}
	} else {
		define("GAME_MODE", "computers");
	}
	
	// Design
	define("LAYOUT", "Basic");
	define("VERSION", "0.4alpha");
	define("STARTING_CASH", $starting_cash);
	
	// Cash
	if ($_SESSION['cash'] < 0 or !isset($_SESSION['cash'])) {
		$_SESSION['cash'] = STARTING_CASH;
	}
	
	// New Game
	if ($_GET['restart'] == 'true') {
		$query = "SELECT `Name`, `Stock` FROM `" . GAME_MODE . "`";
		$result = mysql_query($query) or die("mysql error: " . mysql_error());
		while ($name = mysql_fetch_row($result)) {
			$query = "UPDATE `" . GAME_MODE . "` SET `Stock` = '" . ($name[1] + $_SESSION[$name[0]]) . "' WHERE `Name` = '" . $name[0] . "'";
			$update = mysql_query($query);
		}

		session_destroy();
		session_start();
		header("Location: index.php");
		die;
	}
	
	// Refresh
	if ($_GET['refresh'] == 'true') {
		$query = "SELECT `Name` FROM `" . GAME_MODE . "`";
		$result = mysql_query($query);
		while ($name = mysql_fetch_row($result)) {
			newPrice($name[0]);
		}
	}
	
	// Action Handling
	if (isset($_GET['action'])) {
		$action = mysql_real_escape_string($_GET['action']);
		$material = mysql_real_escape_string($_GET['material']);
		$amount = mysql_real_escape_string($_GET['amount']);
		
		if ($amount < 1 and $amount != 'all') {
			header("Location: index.php?er=3");
			die;
		}
		
		if ($action == "buy") {
			$query = "SELECT `CurrentPrice`, `Stock` FROM `" . GAME_MODE . "` WHERE `Name` = '$material' LIMIT 1";
			$result = mysql_query($query);
			$row = mysql_fetch_row($result);
			
			$price = $row[0];
			$stock = $row[1];
			
			if ($amount == 'all') {
				$amount = round(round($_SESSION['cash']) / round($price)) - 1;
				if ($amount > $stock) { 
					$amount = $stock;
				}
			}
			
			if ($amount > $stock) {
				header("Location: index.php?er=1");
				killyourself();
			}
			
			if ($amount * $price > $_SESSION['cash']) {
				header("Location: index.php?er=2");
				die;
			}
			
			# Buy
			$new_stock = $stock - $amount;
			$new_cash = $_SESSION['cash'] - ($amount * $price);
			$_SESSION['cash'] = $new_cash;
			
			$query = "UPDATE `" . GAME_MODE . "` SET `Stock` = '$new_stock' WHERE `Name` = '$material'";
			$result = mysql_query($query);
			
			$_SESSION[$material] += $amount;
			
			
		} elseif ($action == "sell") {
		
			if ($amount == 'all') {
				$amount = $_SESSION[$material];
			}
		
			if ($amount > $_SESSION[$material]) {
				header("Location: index.php?er=4");
				die;
			}
			
			$query = "SELECT `CurrentPrice`, `Stock` FROM `" . GAME_MODE . "` WHERE `Name` = '$material' LIMIT 1";
			$result = mysql_query($query);
			$row = mysql_fetch_row($result);
			
			$price = $row[0];
			$stock = $row[1];
			
			# Sell
			$new_stock = $stock + $amount;
			$new_cash = $_SESSION['cash'] + ($amount * $price);
			$_SESSION['cash'] = $new_cash;
			
			$query = "UPDATE `" . GAME_MODE . "` SET `Stock` = '$new_stock' WHERE `Name` = '$material'";
			$result = mysql_query($query);
			
			$_SESSION[$material] -= $amount;
			
			
		}
		
		$query = "SELECT `Name` FROM `" . GAME_MODE . "`";
		$result = mysql_query($query);
		while ($name = mysql_fetch_row($result)) {
			newPrice($name[0]);
		}
	}
	
	
	function newPrice($material) {
		$query = "SELECT * FROM `" . GAME_MODE . "` WHERE `Name` = '$material' LIMIT 1";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		
		$name = $row[0];
		$stock = $row[1];
		$minimum = $row[2];
		$maximum = $row[3];
		$price = $row[4];
		$recordmaxstock = $row[5];
		$min_flux = $row[6];
		$max_flux = $row[7];
		
		if ($stock > $recordmaxstock) {
			$recordmaxstock = $stock;
			$query = "UPDATE `" . GAME_MODE . "` SET `RecordMaxStock` = '$recordmaxstock' WHERE `Name` = '$name'";
			$result = mysql_query($query);
		}
		
		$rand = rand(0,1);
		if ($rand == 1) {
			$fluc = rand($min_flux, $max_flux) * -1;
		} elseif ($rand == 0) {
			$fluc = rand($min_flux, $max_flux);
		} else {
			$fluc = 0;
		}
		
		$new_price = (($minimum - $maximum) / (0 - ($recordmaxstock * 1.5))) * $stock + $fluc;
		if ($new_price < $minimum) {
			$new_price = $minimum;
		}
		if ($new_price > $maximum) {
			$new_price = $maximum;
		}
		# Just a linear expression with slight fluctuation
		
		$query = "UPDATE `" . GAME_MODE . "` SET `CurrentPrice` = '$new_price' WHERE `Name` = '$name'";
		$result = mysql_query($query);
		
		
	}
	
	function killyourself() {
		die;
	}
	
	// Cash manipulation for testing lololol
	if ($_GET['password'] == "abecedario") {
		# For controlled debug testing
		$_SESSION['cash'] += 1500;
	}
	
?>