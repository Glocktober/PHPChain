</div> <!-- end detail content -->
</div> <!-- end main table  -->
</div>
</div>
<script>
copyclip = function(el){
	const bg = el;
	const clip = bg.children[0].innerText;
    flashel(bg);
	navigator.clipboard.writeText(clip);
}

inpclip = function(el){
    const clip = el.value;
    flashel(el);
    navigator.clipboard.writeText(clip);
}

flashmes = function(mess){
	const mesbar = document.getElementById('messagebar');
	const curmes = mesbar.innerHTML;
	mesbar.innerHTML = `<span class=success><i class="material-icons w3-large iconoffs">info</i>&nbsp;&nbsp;${mess}</success>`;
	setTimeout(() => {
		mesbar.innerHTML = curmes;
	}, 1500);
}

flashel = function(el){
	const origcolor = el.style.background;
	flashmes('Copied To Clipboard');
	el.style.background = "crimson";
	setTimeout(() => {
		el.style.background = "darkgoldenrod";
	}, 150);
	setTimeout(() => {
		el.style.background = origcolor;
	}, 250);
}
// Set focus on element with class of "focus"
setTimeout(() => {
	const foc = document.getElementsByClassName("focus");
	if (foc) foc[0].focus();
}, 0);
// clear filters
clearFilters = function(){
	const els = document.getElementsByClassName('seafilter');
	n = els.length;
	for (i=0;i<n;i++){
		els[i].value = "";
		els[i].dispatchEvent(new Event('input'));
	}
	flashmes('Filters cleared');
}
</script>
</BODY>
</HTML>
