<?php
/**
* Generating the global parameter
**/
global $post;
$url = get_post_meta($post->ID, '_urlForFeeds', ARRAY_A);

if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
    wp_die($url.' is not a valid URL');
}
	
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$output = curl_exec($ch); 
curl_close($ch);    
//echo $output;
?>

<div style="width: 100%;clear: both;">
	<div style="width: 100%;clear: both;">
		<div style="width:20%;float: left;position: fixed;top: 0;">
			<div><a href="javascript:void(0)" id="0" class="sidebarSelector" onclick="setSeletorType(this)" >SELECT ITEMS WRAPPER</a></div>
			<div><a href="javascript:void(0)" id="1" class="sidebarSelector" onclick="setSeletorType(this)" >SELECT TITLE</a></div>
			<div><a href="javascript:void(0)" id="2" class="sidebarSelector" onclick="setSeletorType(this)" >SELECT IMAGE</a></div>
			<div><a href="javascript:void(0)" id="3" class="sidebarSelector" onclick="setSeletorType(this)" >SELECT DESCRIPTION</a></div>
			<div><a href="javascript:void(0)" onclick="submitSelectors(this)" >SAVE AND GENERATE FEEDS</a></div>
		</div>
		<div id="dynamicContent" style="width:80%;float: right;"><?php echo $output ?></div>
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<style type="text/css">
	.highlight {
		border: 1px solid black !important;
	}
	.highlightWrapper {
		border: 3px solid red !important;
		/*padding: 10px;*/
	}
	.highlightTitle {
		border: 3px solid green !important;
		/*padding: 6px;*/
	}
	.highlightDescription {
		border: 3px solid blue !important;
		/*padding: 3px;*/
	}
	.highlightActiveSelector {
		background-color: cyan !important;
	}
</style>
<script type="text/javascript">
	var selector = null
	var clickCount = 0
	var selectedWrapper = selectedTitle = selectedDescription = null	
	var url = "<?php echo $url ?>";

	function setSeletorType( e ) {
		selector = e.id
		$(".sidebarSelector").removeClass("highlightActiveSelector")
		$(e).addClass("highlightActiveSelector")		
	}
	
	function submitSelectors() {
		console.log(selectedWrapper, selectedTitle, selectedDescription, 'selectedWrapper = selectedTitle = selectedDescription')
	}

	$(document.getElementById("dynamicContent")).click( function(e) {
		e.preventDefault();
		if( !selector ) {
			alert("Please select selector from sidebar")
			return;
		}
		
		// If classes are present
		let classSelectorString = ""
		if( e.target.classList ) {
			let classLists = e.target.classList.value.split(" ")
			if(classLists.length > 0) {
				for (var i = 0; i < classLists.length; i++) {
					if(classLists[i]) {
						classSelectorString += "."+classLists[i]+", "
					}
				}
				classSelectorString += ".k"
			}
			else {
				alert("No class preset in this wrapper please select another element")
			}
			console.log(classSelectorString, 'class lists')
		}

		//clickCount++
		//if(clickCount < 4) {
			if(selector == 0) {
				$(".highlightWrapper").removeClass("highlightWrapper")
				selectedWrapper = classSelectorString
				//selectedWrapper = e.target.tagName
				//e.target.classList.add("highlightWrapper")
				$(`${classSelectorString}`).addClass("highlightWrapper")
				//$("#1").trigger("click")
			}
			else if(selector == 1) {
				$(".highlightTitle").removeClass("highlightTitle")
				selectedTitle = classSelectorString
				//selectedTitle = e.target.tagName
				//e.target.classList.add("highlightTitle")
				$(`${classSelectorString}`).addClass("highlightTitle")
				//$("#2").trigger("click")
			}
			else if(selector == 2) {
				$(".highlightDescription").removeClass("highlightDescription")
				selectedImages = classSelectorString
				//selectedImages = e.target.tagName
				e.target.classList.add("highlightImages")
				//$("#3").trigger("click")
			}
			else if(selector == 3) {
				$(".highlightDescription").removeClass("highlightDescription")
				selectedDescription = classSelectorString
				//selectedDescription = e.target.tagName
				//e.target.classList.add("highlightDescription")
				$(`${classSelectorString}`).addClass("highlightDescription")
			}
		//}		
		// else {
		// 	var queryParamsJson = `wrapperTag=${selectedWrapper}.${selectedWrapperClasses}&titleTag=${selectedTitle}&descriptionTag=${selectedDescription}&url=${url}`
		// 	console.log(queryParamsJson, 'this is clicked')
		// 	window.location.href = "http://localhost/test/index.php?"+queryParamsJson
		// }
	})

	$(document).ready(function() {		

		(function() {
		  var prev;

		  if (document.getElementById("dynamicContent").addEventListener) {
		    document.getElementById("dynamicContent").addEventListener('mouseover', handler, false);
		  }
		  else if (document.getElementById("dynamicContent").attachEvent) {
		    document.getElementById("dynamicContent").attachEvent('mouseover', function(e) {
		      return handler(e || window.event);
		    });
		  }
		  else {
		    document.getElementById("dynamicContent").onmouseover = handler;
		  }

		  function handler(event) {
		    if (event.target === document.getElementById("dynamicContent") || (prev && prev === event.target)) {
		      return;
		    }
		    if (prev) {
		      prev.className = prev.className.replace(/\bhighlight\b/, '');
		      prev = undefined;
		    }
		    if (event.target) {
		      prev = event.target;
		      prev.className += " highlight";
		    }
		  }
		})();

	})

</script>