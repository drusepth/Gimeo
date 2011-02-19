<?php require_once("header.php"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Gimeo Trading Company</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	
	<link rel="shortcut icon" href="tux.ico" /> 
	
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script type="text/javascript" src="script.js"></script>
	
</head>
<body>

	<table>
		<tr>
			<td colspan="6">
				<center>Gimeo Trading Company <span style="color:#ccc;">v.<?php echo VERSION; ?></span></center>
			</td>
		</tr>
		<tr class="centered_row">
			<td>
				<a href="?restart=true">
					<img src="images/arrow_rotate_anticlockwise.png">
					New Game
				</a>
			</td>
			<td colspan="2">
				Game Mode: <strong><?php echo GAME_MODE; ?></strong>
			</td>
			<td colspan="2">
				Current Cash: <img src="images/money_dollar.png"><?php echo round($_SESSION['cash'], 2); ?>
			</td>
			<td>
				<a href="index.php?refresh=true">
					<img src="images/arrow_rotate_clockwise.png">
					Refresh
				</a>
			</td>
		</tr>
		<tr class="header_row">
			<td>
				<strong><img src="images/cart_go.png">Item</strong>
			</td>
			<td>
				<strong><img src="images/coins.png">Price</strong>
			</td>
			<td>
				<strong><img src="images/basket.png">Stock</strong>
			</td>
			<td>
				<strong><img src="images/user_gray.png">You Own</strong>
			</td>
			<td style="text-align:right;">
				<strong><img src="images/calculator.png">Amount</strong>
			</td>
			<td>
				<strong><img src="images/tick.png">Actions</strong>
			</td>
		</tr>
		
		<?php
			$query = "SELECT * FROM `" . GAME_MODE . "`";
			$result = mysql_query($query) or die("mysql error: " . mysql_error());
			while ($row = mysql_fetch_row($result)) {
				$name = $row[0];
				$stock = $row[1];
				$minimum = $row[2];
				$maximum = $row[3];
				$current_price = $row[4];
		?>
				<tr>
					<td>
						<?php echo $name; ?>
					</td>
					<td>
						$<?php echo round($current_price, 2); ?>
					</td>
					<td>
						<?php echo $stock; ?>
					</td>
					<td>
						<?php echo ($_SESSION[$name] > 0) ? $_SESSION[$name] : "0"; ?>
					</td>
					<td>
						<input type="text" value="0" id="<?php echo $name; ?>" style="text-align:right;">
					</td>
					<td>
						<u>
							<a href="javascript:buy('<?php echo $name; ?>');" title="Buy">B</a> 
							[<a href="javascript:buyAll('<?php echo $name; ?>');" title="Buy All">A</a>] / 
							<a href="javascript:sell('<?php echo $name; ?>');" title="Sell">S</a>
							[<a href="javascript:sellAll('<?php echo $name; ?>');" title="Sell All">A</a>]
						</u>
					</td>
				</tr>
		<?
			}
		?>
		
		<?php
			if (isset($_GET['er'])) {
		?>
		<tr>
			<td colspan="6" id="error_bar">
				<strong>Error</strong>: 
				<?php
					$er = $_GET['er'];
					if ($er == '1') {
						echo "The store doesn't have that much in stock!";
					} elseif ($er == '2') {
						echo "You don't have enough money!";
					} elseif ($er == '3') {
						echo "You can't buy nothing, silly!";
					} elseif ($er == '4') {
						echo "You don't have that much stuff to sell!";
					}
				?>
			</td>
		</tr>
		<?php
			}
		?>
		
	</table>

</body>
</html>