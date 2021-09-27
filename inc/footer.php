</TD>
</TR>
</TABLE>
<input type="text" id="blind" class="blind">
</div>
<script>
var inp = document.getElementById("blind");

copyTd2Clipboard = function(e){
	setClipboard(this.innerText);
}

setClipboard = function(txt){

	navigator.clipboard.writeText(txt)
	.then( ()=>{
		console.log(`copied ${txt} to clipboard`)
	})
	.catch((error) =>{
		console.log(`copy to clipboard failed: ${error}`)
	} )
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
