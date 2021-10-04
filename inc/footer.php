</div> <!-- end detail content -->
</div> <!-- end main table  -->
</div>
</div><!-- end of outer structure div  -->
<script>
// Copy text from the first child (a span) of a TD
copyclip = function(el){
	const bg = el;
	const clip = bg.children[0].innerText;
    flashel(bg);
	navigator.clipboard.writeText(clip);
}

// Copy text from input to clipboard
inpclip = function(el){
    const clip = el.value;
    flashel(el);
    navigator.clipboard.writeText(clip);
}

// Flash a message
flashmes = function(mess, delay=2000){
	const mesbar = document.getElementById('messagebar');
	const curmes = mesbar.innerHTML;
	mesbar.innerHTML = `<span class=success><i class="material-icons iconoffs">info</i>&nbsp;&nbsp;${mess}</success>`;
	setTimeout(() => {
		mesbar.innerHTML = curmes;
	}, delay);
}

// Flash an element and announce it was copied to the clipboard
flashel = function(el){
	const origcolor = el.style.background;
	flashmes('Copied To Clipboard', 1200);
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
