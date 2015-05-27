    var flag = false;
    
    var ALARM = [];
    
    var video_id = "9bZkp7q19f0";
    var video_title = 'NaN';
    
    
    //Now time
    var h;
    var m;
    var s;
    
    // Alarm time
    var h_a;
    var m_a;
    var s_a;
    
    var interval = 1;

    function startTime()
    {
        var tm=new Date();
        h=tm.getHours();
        m=tm.getMinutes();
        s=tm.getSeconds();
        var time = document.getElementById('time');
        time.innerHTML = checkTime(h)+":"+checkTime(m)+":"+checkTime(s); 
        
        if(!flag)
        {
            h_a=tm.getHours();
            m_a=tm.getMinutes();
            s_a=tm.getSeconds();
            var time_a = document.getElementById('time_a'); 
            time_a.innerHTML = checkTime(h)+":"+checkTime(m)+":"+checkTime(s);
            
            flag = true;
        }
        
        var al = document.getElementById('alarms');
        var altr = al.getElementsByTagName('tr');
        if(altr.length == 0)
        {
            al.innerHTML = '<tr id="clear"><td style="text-align: center; font-size: 20px; color: #ccc"><br /> ALARMS NOT FOUND</td></tr>'
        }
        
        for(var i=0; i<ALARM.length; i++)
        {
            if(ALARM[i] !== undefined)
            {
                if(h == ALARM[i][1] && m == ALARM[i][2] && s == ALARM[i][3])
                {   
                    var block = document.getElementById('big_v_block');

                    if(block)
                    {
                        block.innerHTML = '<div id="big_v"></div>';
                    }
                    
                    changeVideo(true, true, ALARM[i][0]);
                    
                    var alarms = document.querySelectorAll('#alarms a'); 
                    
                    for(var v = 0; v < alarms.length; v++)
                    {
                        if(v == i)
                        {
                            var id = alarms[v].getAttribute('id').split('_');
                            
                            delete ALARM[id[1]];
                            var div = document.getElementById('video_'+id[1]);
                            div.parentNode.removeChild(div);
                        }
                    }
                }
            }
        }

        t=setTimeout(startTime,500);
    }
    
    function checkTime(i)
    {
        if (i<10)
        {
        i="0" + i;
        }
        return i;
    }
    
    startTime()
    
    
    var snoozing = document.querySelectorAll('.snoozing button');
    
    for(var i = 0; i < snoozing.length; i++)
    {
        snoozing[i].addEventListener('click', function(e)
        {
            console.log('click')
            var id = this.getAttribute('id').split('_');
            
            for(var i=0; i<ALARM.length; i++)
            {
                if(ALARM[i] !== undefined)
                {
                    if(ALARM[i][0] == video_id)
                    {
                        console.log(ALARM)
                        var p = parseInt(id[1]);
        console.log(p)        
                        var sum = ALARM[i][2] + p;
        console.log(sum)
                        if(sum > 59)
                        {
                            ALARM[i][2] = 0 + (sum - 59) - 1
                            ALARM[i][1]++;
                            if(ALARM[i][1] > 23) ALARM[i][1] = 0;
                        }
                        else
                        {
                            ALARM[i][2] = ALARM[i][2] + p;
                        }
                        console.log(ALARM)
                    }
                }
            }
            e.preventDefault();
        })
    }
    
    
    var up_arrows =document.querySelectorAll('.up_arrows img');
    for(var i = 0; i < up_arrows.length; i++)
    {
        up_arrows[i].addEventListener('mousedown', function(e){
            e.preventDefault();
        })
    }
    
    
    for(var i = 0; i < up_arrows.length; i++)
    {
        up_arrows[i].addEventListener('mouseup', function(e){
            e.preventDefault();
        })
    }
    
    
    var down_arrows =document.querySelectorAll('.down_arrows img');
    for(var i = 0; i < down_arrows.length; i++)
    {
        down_arrows[i].addEventListener('mousedown', function(e){
            e.preventDefault();
        })
    }
    
    for(var i = 0; i < down_arrows.length; i++)
    {
        down_arrows[i].addEventListener('mouseup', function(e){
            e.preventDefault();
        })
    }
    
    
    var up_arrows_a =document.querySelectorAll('.up_arrows a');
    for(var i = 0; i < up_arrows_a.length; i++)
    {
        up_arrows_a[i].addEventListener('click', function(e){
            var type = this.getAttribute('id')
        
            if(type == 'h'){
                h_a++;
                if(h_a > 23) h_a = 0;
            }
            if(type == 'm'){
                m_a++;
                if(m_a > 59){
                    m_a = 0;
                    h_a++;
                    if(h_a > 23) h_a = 0;
                }
            }
            if(type == 's'){
                s_a++;
                if(s_a > 59){
                    s_a = 0;
                    m_a++;
                    if(m_a > 59){
                        m_a = 0;
                        h_a++;
                        if(h_a > 23) h_a = 0;
                    }
                }
            }
           var time_a = document.getElementById('time_a'); 
            time_a.innerHTML = checkTime(h_a)+":"+checkTime(m_a)+":"+checkTime(s_a);
            e.preventDefault();
        })
    }
    
    var down_arrows_a =document.querySelectorAll('.down_arrows a');
    for(var i = 0; i < down_arrows_a.length; i++)
    {
        down_arrows_a[i].addEventListener('click', function(e){
            var type = this.getAttribute('id')
         
            if(type == 'h'){
                h_a--;
                if(h_a < 0) h_a = 23;
            }
            if(type == 'm'){
                m_a--;
                if(m_a < 0){
                    m_a = 59;
                    h_a--;
                    if(h_a < 0) h_a = 23;
                }
            }
            if(type == 's'){
                s_a--;
                if(s_a < 0){
                    s_a = 59;
                    m_a--;
                    if(m_a < 0){
                        m_a = 59;
                        h_a--;
                        if(h_a < 0) h_a = 23;
                    }
                }
            }
            var time_a = document.getElementById('time_a'); 
            time_a.innerHTML = checkTime(h_a)+":"+checkTime(m_a)+":"+checkTime(s_a);
            e.preventDefault();
        })
    }
    
    
    var time_buttons =document.querySelectorAll('.time_buttons button');
    for(var i = 0; i < time_buttons.length; i++)
    {
        time_buttons[i].addEventListener('click', function(e){
            var type = this.getAttribute('id').split('_');
        
            if(type[1] == 'update')
            {
                h_a = h;
                m_a = m;
                s_a = s;
                
                var time_a = document.getElementById('time_a'); 
                time_a.innerHTML = checkTime(h)+":"+checkTime(m)+":"+checkTime(s);
            }
            else
            {
                var p = parseInt(type[1]);
                
                var sum = m_a + p;

                if(sum > 59)
                {
                    m_a = 0 + (sum - 59) - 1
                    h_a++;
                    if(h_a > 23) h_a = 0;
                }
                else
                {
                    m_a = m_a + p;
                }
                
                var time_a = document.getElementById('time_a'); 
                time_a.innerHTML = checkTime(h_a)+":"+checkTime(m_a)+":"+checkTime(s_a);
            }
            e.preventDefault();
        })
    }
    
    document.getElementById('select_url').addEventListener('change', function(){
        video_id = this.value;
        getVideoName(video_id);
        changeVideo(false, false, '')
    })
    document.getElementById('start_alarm').addEventListener('click', function(){

        ALARM[ALARM.length] = [video_id, h_a, m_a, s_a];
        
        var alDOM = document.getElementById('alarms');
        
        var clear = document.getElementById('clear');
        
        if(clear)
        {
            clear.parentNode.innerHTML = '';
        }
        
        alDOM.innerHTML = alDOM.innerHTML + '<tr id="video_'+(ALARM.length-1)+'"><td>ALARM TIME: <b>'+ checkTime(h_a) +':'+ checkTime(m_a) +':'+ checkTime(s_a) +'</b></td> <td>VIDEO: <b>'+video_title+'</b></td> <td> <a href="#" id="del_'+(ALARM.length-1)+'">DELETE</a></td></tr>';
        
        var alarms =document.querySelectorAll('#alarms a'); 
        for(var i = 0; i < alarms.length; i++)
        {
            alarms[i].addEventListener('click', function(e){
                var id = this.getAttribute('id').split('_');
                console.log(ALARM[id[1]])
                delete ALARM[id[1]];
                var div = document.getElementById('video_'+id[1]);
                div.parentNode.removeChild(div);
                e.preventDefault();
            })
        }
    })
    /*
    var alarms =document.querySelectorAll('#alarms a');
    console.log(alarms)
    for(var i = 0; i < alarms.length; i++)
    {
        alarms[i].addEventListener('click', function(e){
            var id = this.getAttribute('id').split('_');
            console.log(ALARM[id[1]])
            delete ALARM[id[1]];
            var div = document.getElementById('video_'+id[1]);
            div.parentNode.removeChild(div);
            e.preventDefault();
        })
    }
    */
    function getVideoName(v_id)
    {
        getJSON('https://gdata.youtube.com/feeds/api/videos/'+v_id+'?v=2&alt=jsonc').then(function(data) {
            video_title = data.data.title
        }, function(status) {
            video_title = 'NaN'
        });
    }
    
    var getJSON = function(url) {
      return new Promise(function(resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('get', url, true);
        xhr.responseType = 'json';
        xhr.onload = function() {
          var status = xhr.status;
          if (status == 200) {
            resolve(xhr.response);
          } else {
            reject(status);
          }
        };
        xhr.send();
      });
    };
    
    

    var params = { allowScriptAccess: "always" };
    var atts = { id: "myytplayer" };
    swfobject.embedSWF("https://www.youtube.com/v/"+video_id+"?enablejsapi=1&playerapiid=ytplayer&version=3",
                       "vb", "705", "400", "8", null, null, params, atts);
    getVideoName(video_id);

    document.getElementById('test_clip').addEventListener('click',function(e){
            changeVideo(true, false, '');
        
    })
    
    document.getElementById('stop_playing').addEventListener('click',function(e){
            changeVideo(false, false, '');
        
    })
    
    document.getElementById('set_url').addEventListener('click',function(e){
        var url = document.getElementById('url_area'); 
        parseUrl(url.value);
        video_id = parseUrltest(url.value);
        getVideoName(video_id);
        changeVideo(false, false, '');
    })
    
    
    document.getElementById('update_time').addEventListener('click',function(e){
        var tm=new Date();
        h_a=tm.getHours();
        m_a=tm.getMinutes();
        s_a=tm.getSeconds();
        var time_a = document.getElementById('time_a'); 
        time_a.innerHTML = checkTime(h)+":"+checkTime(m)+":"+checkTime(s);
        e.preventDefault();
    })
    
    /*document.getElementById('browser_url').addEventListener('click', function(e){
        var th = document.getElementById('yt1');
        th.style.display = 'none';
        var th = document.getElementById('yt');
        th.style.display = 'block';
    })*/
    
    function changeVideo(play_v, param, video_id_p)
    {
        var params = { allowScriptAccess: "always" };
        var atts = { id: "myytplayer" };
        var str = '';
        if(play_v)
            str = '&autoplay=1';
        
        if(video_id_p != '')
        {
            video_id = video_id_p;
            getVideoName(video_id_p);
        }
        if(param)
        {
            var right = document.getElementById('right');
            right.style.display = 'none';
            
            var left = document.getElementById('left');
            left.style.display = 'none';
            
            var center = document.getElementById('center');
            center.style.display = 'block';
            
            var vid = document.getElementById('myytplayer');
            vid.innerHTML = '';
            
            swfobject.embedSWF("https://www.youtube.com/v/"+video_id+"?enablejsapi=1&playerapiid=ytplayer&version=3&loop=1"+str,
                           "big_v", "970px", "550", "8", null, null, params, atts);
        }
        else
        {
            swfobject.embedSWF("https://www.youtube.com/v/"+video_id+"?enablejsapi=1&playerapiid=ytplayer&version=3"+str,
                               "myytplayer", "705", "400", "8", null, null, params, atts);
        }
        return;
    }
    
    function parseUrl(url)
    {
        url = url.split('=');
        video_id = url[1]; 
    }
    
    function parseUrltest(url)
    {
        url = url.split('=');
        return url[1]; 
    }
    
    document.getElementById('big_stop').addEventListener('click', function(e){
        
        var block = document.getElementById('big_v_block');
        
        block.innerHTML = '<div id="big_v"></div>';
        
        var right = document.getElementById('right');
        right.style.display = 'block';

        var left = document.getElementById('left');
        left.style.display = 'block';

        var center = document.getElementById('center');
        center.style.display = 'none';

        changeVideo(false, false, '');
    })
    
    
    /*
    document.getElementById('search').addEventListener('click', function(){
        var q = document.getElementById('y_search').value;
        console.log(gapi)
        var request = gapi.client.youtube.search.list({
          q: q,
          part: 'snippet'
        });
        
        request.execute(function(response) {
          var str = JSON.stringify(response.result);
          document.getElementById('search_block').innerHTML = "<pre>"+str+"</pre>";
        });
    })*/