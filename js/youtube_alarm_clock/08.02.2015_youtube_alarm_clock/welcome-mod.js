if( !localStorage["_welcome-mod.welcome_displayed"] ){
	
	localStorage["_welcome-mod.welcome_displayed"] = true;
	
	chrome.management.get( "llaficoajjainaijghjlofdfmbjpebpa", function( ext ){
		
		var url = "http://flashvideodownloader.org/fvd-suite/to/s/wlcwidsd/";
		
		if( !ext ){
			url = "http://flashvideodownloader.org/fvd-suite/to/s/wlcwidnosd/";
		}
		
		chrome.tabs.create({
			url: url,
			active: true
		});
		
	} );
	
}
