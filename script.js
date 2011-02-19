function buy(material) {
	var amount = this.document.getElementById(material).value;
	var url = "index.php?action=buy&material=" + material + "&amount=" + amount;
	document.location = url;
}
function sell(material) {
	var amount = this.document.getElementById(material).value;
	var url = "index.php?action=sell&material=" + material + "&amount=" + amount;
	document.location = url;
}

function buyAll(material) {
	var amount = this.document.getElementById(material).value;
	var url = "index.php?action=buy&material=" + material + "&amount=all";
	document.location = url;
}
function sellAll(material) {
	var amount = this.document.getElementById(material).value;
	var url = "index.php?action=sell&material=" + material + "&amount=all";
	document.location = url;
}