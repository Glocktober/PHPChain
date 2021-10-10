</div> <!-- end detail content -->
</div> <!-- end main table  -->
</div>
</div><!-- end of outer structure div  -->
<!-- Modal dialog for flash notify  -->
<div class="w3-modal" id="flashmodal">
    <div class="w3-modal-content" id="flashmessmodal" style="width:40%; word-break:break-word;">
        <div class="w3-container">
			<div class="w3-panel w3-center w3-border w3-border-red w3-padding">
				<p class="w3-large" id="flashmess"></p>
			</div>
        </div>
    </div>
</div> <!-- End notify flash -->
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
    const dia = document.getElementById('flashmodal');
    const passmes = document.getElementById('flashmess');
    passmes.innerHTML = mess;
    dia.style.display = 'block';
    setTimeout(() => {
        dia.style.display = 'none';
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
	flashmes('Filters cleared', 500);
}

// toggle nav pane
var chainnav = true;
toggleNav = function(){
	const navshow = document.getElementById('navshowmenu');
	if (chainnav){
		// closing nav menu
		chainnav = false;
		w3.addClass('#navpane', 'w3-hide');
		w3.addClass('#detailpane', 'fullw');
		navshow.style.transform = 'rotate(180deg)';
		navshow.style.color = 'red';
		navshow.setAttribute('title','Click to show folder menu');
	} else {
		// Opening nav menu
		chainnav = true;
		w3.removeClass('#detailpane', 'fullw');
		w3.removeClass('#navpane','w3-hide');
		navshow.style.transform = 'rotate(0deg)';
		navshow.style.color = 'green';
		navshow.setAttribute('title', 'Click to hide folder menu');
	}
}

// sort navigator menu on startup
w3.sortHTML('ul#catlist', 'li'); 
</script>
</BODY>
</HTML>
