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
	var tds = document.getElementsByClassName("password");
	for (var i in tds)  tds[i].onclick = copyTd2Clipboard; 
}
setUp();

</script>
</BODY>
</HTML>
