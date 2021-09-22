</TD>
</TR>
</TABLE>
<input type="text" id="blind" class="blind">
<script>
var inp = document.getElementById("blind");

copyTd2Clipboard = function(e){
	setClipboard(this.innerText);
}

setClipboard = function(txt){
	console.log("sc called with " + txt);
	inp.value = txt;
	inp.select();
	document.execCommand("copy");
	inp.value = "";
	inp.blur();
}

setUp = function(){
	const tds = document.getElementsByClassName("copyclick");
	for (var i in tds)  tds[i].onclick = copyTd2Clipboard;
	const foc = document.getElementsByClassName("focus");
	foc[0].focus();
}
setUp();

</script>
</BODY>
</HTML>
