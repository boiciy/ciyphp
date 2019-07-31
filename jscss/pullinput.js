(function(a) {
    a.fn.pull = function(D) {
        var styleElement = document.getElementById('style_pull');
        if (!styleElement) {
            styleElement = document.createElement('style');
            styleElement.type = 'text/css';
            styleElement.id = 'style_pull';
            styleElement.innerText = '.pullselect{position: absolute;z-index:100;display:none;border:1px solid #e4e4e4;}.pullselect>li{line-height:1.3em;padding:0.5em 1em;background:#f6f6f6;transition: all .5s;cursor:pointer;}.pullselect>li:hover{background:#ffffff;}.pullselect>div{font-size:0.6em;line-height:1.2em;height:1.2em;overflow: hidden;background: #f0f0f0;padding: 0 1em;cursor:pointer;}';
            document.getElementsByTagName('head')[0].appendChild(styleElement);
        }
        D.maxlist = D.maxlist||8;//最多显示列数
        D.ulext = D.ulext||' style="bottom:3em;"';//bottom:3em;  margin-top:0.2em;
        D.lifunc = D.lifunc||function(val,cata){
            if(val == '' || cata.title.toUpperCase().indexOf(val) > -1 || cata.codeid.toUpperCase().indexOf(val) > -1)
                return '<li>'+cata.title+'</li>';
            return '';
        };
        D.valfunc = D.valfunc||function(li){
            return $(li).text();
        };
        var uldom = $('<ul class="pullselect"'+D.ulext+'></ul>');
        var inputdom = $(this);
        inputdom.after(uldom);
        inputdom.on('focus',function(){
            setTimeout(function(){
                inputdom.trigger("keyup");
                uldom.slideDown(100);
                $(document).one("click", function(){
                    uldom.slideUp(50);
                });
            },300);
        }).on('click',function(){
            inputdom.trigger("keyup");
        }).on('keyup',function(e){
            var val = inputdom.val();
            if(uldom.is(":hidden"))
                uldom.slideDown(100);
            var html = '';
            var cnt = 0;
            for(var i in D.catadata)
            {
                if(cnt>=D.maxlist)
                {
                    html += '<div>■ ■ ■</div>';
                    break;
                }
                var lih = D.lifunc(val.toUpperCase(),D.catadata[i]);
                if(lih != '')
                    cnt++;
                html += lih;
            }
            if(e.keyCode == 13 && cnt == 1)
            {
                inputdom.val(D.valfunc($(html)));
                uldom.slideUp(50);
                return;
            }
            
            html = $(html).on('click',function(){
                if(this.nodeName == 'DIV')
                {
                    D.maxlist+=5;
                    setTimeout(function(){
                        inputdom.trigger("keyup");
                    },110);
                }
                else
                {
                    inputdom.val(D.valfunc(this));
                    uldom.slideUp(50);
                }
            });
            uldom.html(html);
        });
    }
})($);