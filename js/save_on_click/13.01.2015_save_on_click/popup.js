window.onload = function() {
    
    getSettings();
    
    var reset = document.getElementById('reset');
    var clear = document.getElementById('clear');
    var power = document.getElementById('power');
    var power_var = true;
    
    function getSettings()
    {
        return chrome.storage.local.get({counter: 0, power: 1}, function(items){
            var counter = document.getElementById('counter');
            counter.innerHTML = items.counter;
            
            power_var = items.power;
            
            if(power_var)
            {
                power.innerHTML = 'Disable addon';
            }
            else
            {
                power.innerHTML = 'Enable addon';
            }
        });
    }
    
    function resetCounter(e) {
        chrome.storage.local.set({
            counter: 0,
        }, function(){
            counter.innerHTML = 1;
        });
    }
    
    function clearPage(e)
    {
        chrome.tabs.query({active: true, currentWindow: true}, function (tabs){
            chrome.tabs.sendMessage(tabs[0].id, {action: "clearPage"});
        });
    }
    
    
    function powerAddon(e)
    {
        chrome.storage.local.get({power: 1}, function(items){
            power_var = items.power;
        });

        if(!power_var)
        {
            chrome.storage.local.set({
                power: true,
            }, function(){
                power_var = true;
                power.innerHTML = 'Disable addon';
            });
        }
        else
        {
            chrome.storage.local.set({
                power: false,
            }, function(){
                power_var = false;
                power.innerHTML = 'Enable addon';
            });
        }
    }
    
    reset.addEventListener('click', resetCounter, false);
    clear.addEventListener('click', clearPage, false);
    power.addEventListener('click', powerAddon, false);
}