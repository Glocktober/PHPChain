</div> <!-- end detail content -->
</div> <!-- end main table  -->

</div>
</div>
<script>
flashel = function(el){
	const origcolor = el.style.background;
	el.style.background = "crimson";
	setTimeout(() => {
		el.style.background = "darkgoldenrod";
	}, 150);
	setTimeout(() => {
		el.style.background = origcolor;
	}, 250);
}
setTimeout(() => {
	const foc = document.getElementsByClassName("focus");
	if (foc) foc[0].focus();
}, 0);
</script>
</BODY>
</HTML>
