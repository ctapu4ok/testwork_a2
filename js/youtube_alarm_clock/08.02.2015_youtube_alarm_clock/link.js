var info = {
    width: 200,
    height: 200,
    path: "/widget.html"
};

var fvdSpeedDialInfo = {
	name: "Speed Dial [FVD] - New Tab Page, 3D, Sync...",
	id: "llaficoajjainaijghjlofdfmbjpebpa"
};

function sendInfoResponse( id ){
    chrome.extension.sendMessage(
        id, {
        action: "fvdSpeedDial:Widgets:Widget:setWidgetInfo",
        body: info
    });
}

chrome.extension.onMessageExternal.addListener(function (request, sender, sendResponse) {
		
    if (request && request.action == "fvdSpeedDial:Widgets:Server:isWidget") {
		sendInfoResponse( sender.id );
    }
	
});

// search for speed dial
chrome.management.getAll(function( addons ){
	
	addons.forEach( function( addon ){
		
		if( fvdSpeedDialInfo.id == addon.id || fvdSpeedDialInfo.name == addon.name ){
			
			sendInfoResponse( addon.id );
			
			return false;
		}		
		
	} );
	
});
