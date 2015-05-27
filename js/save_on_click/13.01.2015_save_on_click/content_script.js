$(document).ready(function()
{   
   var counter = 0;
   var power = true;
   
   var types = ['jpg', 'gif', 'png'];
   
   setInterval(function(){
       chrome.storage.local.get({counter: 0, power: 1}, function(items){
            counter = items.counter;
            power = items.power;
       });
   }, 100);
   
   $('img').click(function(e)
   {
        if(power)
        {
            if(!window.event.ctrlKey && e.which != 2)
            {
                if($(this).parent('a').length > 0)
                {
                    $(this).parent('a').click(function(ev)
                    {
                        ev.preventDefault();
                    });
                }
            
                saveImage($(this).attr('src'), false);
                            
                $(this).css({'opacity' : '0.1'});
                            
                if($(this).parent('a').length > 0)
                {
                    saveImage($(this).parent('a').attr('href'), true);            
                }
                
                counter++;
                chrome.storage.local.set({counter: counter});
            }
            else
            {
                if($(this).parent('a').length > 0)
                {
                    window.open(
                        $(this).parent('a').attr('href'),
                        '_blank'
                    );
                }
            }
        }
   });
   
   function saveImage(url, large)
   {
        window.URL = window.URL || window.webkitURL;

        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.responseType = 'blob';
         
        xhr.onload = function(e) {
          if (this.status == 200) {
            var blob = this.response;
        
            SaveToDisk(blob, large, url);
          }
        };
            
        xhr.send();
   }
   
   function SaveToDisk(blobURL, large, url) {
        var reader = new FileReader();
        reader.readAsDataURL(blobURL);
        reader.onload = function (event) {
            
            var file_ext = blobURL.type.split('/');
            
            if(file_ext[0] == 'image')
            {
                if(url.indexOf('jpg')+1 && file_ext[1] == 'jpeg')
                {
                    file_ext[1] = 'jpg';
                }
                chrome.runtime.sendMessage({method: "getLocalStorage", key: "counter"}, function(response) {
                    counter = response.data;
                    power = response.power;
                })
                
                if(!large)
                    var filename = counter+'.'+file_ext[1]
                else
                    var filename = counter+'_large.'+file_ext[1]
                    
                var save = document.createElement('a');
                save.href = event.target.result;
                save.target = '_blank';
                save.download = filename;
    
                var event = document.createEvent('Event');
                event.initEvent('click', true, true);
                save.dispatchEvent(event);
                (window.URL || window.webkitURL).revokeObjectURL(save.href);
            }
        };
    }
    
    chrome.runtime.onMessage.addListener(function (request, sender, sendResponse){
        if(request.action == 'clearPage')
        {
            $('img').css({'opacity' : '1'});
        }
    });
    
    window.addEventListener("keydown", keyboardNavigation, false);

    function keyboardNavigation(e) {
        if(event.keyCode == 120) {
            $('img').css({'opacity' : '1'});
        }
        if(event.keyCode == 121) {
            chrome.runtime.sendMessage({method: "resetCounter"});
            counter = 0;
        }
        if(event.keyCode == 119) {
            chrome.storage.local.get({power: 1}, function(items){
                power = items.power;
            });
    
            if(!power)
            {
                chrome.storage.local.set({
                    power: true,
                }, function(){
                    power = true;
                });
            }
            else
            {
                chrome.storage.local.set({
                    power: false,
                }, function(){
                    power = false;
                });
            }
        }
    }

});

