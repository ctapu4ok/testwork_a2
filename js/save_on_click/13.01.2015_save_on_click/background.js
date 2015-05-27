var counter;
var power;


chrome.storage.local.get({power: 1}, function(items){
    power = items.power;
});

if(!power || power == 'undefined')
{
    chrome.storage.local.set({
        power: true,
    });
}

chrome.tabs.onUpdated.addListener(function(tabid, info, tab) {
    
    if (info.status == "complete") {
        chrome.tabs.executeScript(tab.id, {
            file: "js/jquery.js"
        }, function() {
            if (chrome.runtime.lastError) {
                console.error(chrome.runtime.lastError.message);
            }
        });
       chrome.tabs.executeScript(tab.id, {
            file: "content_script.js"
        }, function() {
            if (chrome.runtime.lastError) {
                console.error(chrome.runtime.lastError.message);
            }
        });
    }
});

chrome.runtime.onMessage.addListener(function(request, sender, sendResponse) {
    if(request.method == 'getLocalStorage')
    {
        chrome.storage.local.get({counter: 0, power: 1}, function(items){
            counter = items.counter;
            power = items.power;
        });
        sendResponse({data: counter, power:power});
    }
    
    if(request.method == 'resetCounter')
    {
        chrome.storage.local.set({counter: 0});
    }
});