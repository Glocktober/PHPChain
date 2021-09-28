</div> <!-- end detail content -->
</div> <!-- end main table  -->

</div>
</div>
<script>

copyTd2Clipboard = function(e){
	navigator.clipboard.writeText(this.innerText);
}

setUp = function(){
	const tds = document.getElementsByClassName("copyclick");
	for (var i in tds)  tds[i].onclick = copyTd2Clipboard;
	const foc = document.getElementsByClassName("focus");
	if (foc) foc[0].focus();
}
setUp();

</script>
</BODY>
</HTML>
