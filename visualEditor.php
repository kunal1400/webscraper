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
		<div style="width:20%;float: left;position: fixed;top: 0;border-right: 1px solid #FF9F63;height: 100%;background-color: #FED587;z-index: 99999999999;">
			<h3 style="text-align: center; padding: 20px;background-color: #FF9F63; color: #ffffff;">Use these selectors</h3>
			<div style="margin-top: 10px;"><a href="javascript:void(0)" id="0" class="sidebarSelector" onclick="setSeletorType(this)" >Select a News item</a></div>
			<hr/>
			<div style="margin-top: 10px;"><a href="javascript:void(0)" id="1" class="sidebarSelector" onclick="setSeletorType(this)" >Headline</a></div>
			<div style="margin-top: 10px;"><a href="javascript:void(0)" id="2" class="sidebarSelector" onclick="setSeletorType(this)" >Illustration</a></div>
			<div style="margin-top: 10px;"><a href="javascript:void(0)" id="3" class="sidebarSelector" onclick="setSeletorType(this)" >Summary</a></div>
			<hr/>
			<div style="margin-top: 10px;"><a href="javascript:void(0)" class="generateRss" onclick="submitSelectors(this)" >Generate RSS</a></div>
		</div>
		<div style="width:80%;float: right;overflow: scroll;">
			<h3 style="text-align: center; padding: 20px;background-color: #F39355; color: #ffffff;">To create parsing pattern, Click on elements you want to be in your RSS</h3>
			<div id="dynamicContent"><?php echo $output ?></div>
		</div>
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<style type="text/css">
	.generateRss {
		display: block;
	    margin: 15px auto 0;
	    font-size: 16px;
	    height: auto;
	    line-height: 16px;
	    width: auto;
	    padding: 15px 25px;
	    background-color: rgba(86, 201, 144, 0.2);
	    border: 1px solid #57bb89;
	    color: inherit;
	}
	.sidebarSelector {
		padding: 10px 0 10px 15px !important;
		width: 100%;
    	display: inline-block;
		/*margin-top: 5px !important;*/
	}
	.highlight {
		border: 3px solid black !important;
	}
	/*.highlight > * {
		padding: 5px !important;
	}*/
	.highlightWrapper {
		border: 3px solid red !important;
		/*padding: 10px;*/
	}
	.highlightTitle {
		border: 3px solid green !important;
		/*padding: 6px;*/
	}
	.highlightThumbnail {
		border: 3px solid orange !important;
		/*padding: 3px;*/
	}
	.highlightDescription {
		border: 3px solid blue !important;
		/*padding: 3px;*/
	}
	.highlightActiveSelector {
		background-color: #57bb89 !important;    	
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
		if(selectedWrapper && selectedTitle && selectedDescription) {
			$(".generateRss").text("Generating...")
			var queryParamsJson = {
				"action" : 'wp_scrapper_update_selectors',
				"postId": <?php echo $post->ID ?>,
				"selectedWrapper": selectedWrapper,
				"selectedTitle": selectedTitle,
				"selectedDescription": selectedDescription
			}
			// var queryParamsJson = "action=wp_scrapper_update_selectors&postId=<?php echo $post->ID ?>&selectedWrapper="+selectedWrapper+"&selectedTitle="+selectedTitle+"&selectedDescription="+selectedDescription;			
	        jQuery.ajax({	            
	            url    : "<?php echo admin_url( 'admin-ajax.php' ) ?>",
	            data   : queryParamsJson,			    
		        type: 'POST',
	            success: function(data) {
	            	console.log(data)
					$(".generateRss").text("Generated")	            	
	                //jQuery("#feedback").html(data);
	                //alert("success")
	                window.location.href = "<?php echo get_permalink($post) ?>?screen=output"
	            },
	            error: function(error) {
	            	alert("Error")
	            	$(".generateRss").text("Generate Again")	            	
	            	console.log(error)
	            }
	        });
		}
		else {
			alert("Please select news item, heading and Summary")
		}		
		// 	console.log(queryParamsJson, 'this is clicked')
		// 	window.location.href = "http://localhost/test/index.php?"+queryParamsJson
	}

	$(document.getElementById("dynamicContent")).click( function(e) {
		e.preventDefault();
		if( !selector ) {
			alert("Please select an item from sidebar")
			return;
		}
		
		// If classes are present
		let classSelectorString = ""
		if( e.target.classList ) {
			let classLists = e.target.classList.value.split(" ")
			if(classLists.length > 0) {
				for (var i = 0; i < classLists.length; i++) {
					if(classLists[i] && classLists[i] != 'highlight') {
						if(i == 0) {
							classSelectorString += "."+classLists[i]
						}
						else {
							classSelectorString += ", ."+classLists[i]
						}
					}
					// else if(classLists[i] != 'highlight') {
					// 	continue;
					// }
					else {
						// It means that this element has only our added 'highlight' class so break this loop and store its element
						if(classLists[i] != 'highlight' && classSelectorString == "") {
							classSelectorString += e.target.tagName.toLowerCase()
							break;
						}
					}
				}
			}
			else {
				alert("Selector class length is smaller or equals to 0")
			}
		}
		else {
			classSelectorString = e.target.tagName.toLowerCase()
			console.log("__No class present for this selector so taking their element__")
		}
		console.log(classSelectorString, 'class lists')

		//clickCount++
		//if(clickCount < 4) {
			if(selector == 0) {
				$(".highlightWrapper").removeClass("highlightWrapper")				
				selectedWrapper = classSelectorString
				//selectedWrapper = e.target.tagName
				//e.target.classList.add("highlightWrapper")
				$("#dynamicContent").find(`${classSelectorString}`).addClass("highlightWrapper")				
				//$("#1").trigger("click")
			}
			else if(selector == 1) {
				$(".highlightTitle").removeClass("highlightTitle")
				selectedTitle = classSelectorString
				//selectedTitle = e.target.tagName
				//e.target.classList.add("highlightTitle")
				$("#dynamicContent").find(`${classSelectorString}`).addClass("highlightTitle")
				//$("#2").trigger("click")
			}
			else if(selector == 2) {
				$(".highlightThumbnail").removeClass("highlightThumbnail")
				selectedImages = classSelectorString
				//selectedImages = e.target.tagName
				$("#dynamicContent").find(`${classSelectorString}`).addClass("highlightThumbnail")
				//$("#3").trigger("click")
			}
			else if(selector == 3) {
				$(".highlightDescription").removeClass("highlightDescription")
				selectedDescription = classSelectorString
				//selectedDescription = e.target.tagName
				//e.target.classList.add("highlightDescription")
				$("#dynamicContent").find(`${classSelectorString}`).addClass("highlightDescription")
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