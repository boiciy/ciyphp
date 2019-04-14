(function(a) {
    a.fn.upload = function(D) {
        var styleElement = document.getElementById('style_upload');
        if (!styleElement) {
            styleElement = document.createElement('style');
            styleElement.type = 'text/css';
            styleElement.id = 'style_upload';
            styleElement.innerText = '.upload,.upload li{margin:0;padding:0;list-style-type: none}.upload{border:1px solid #d7d7d7;padding:15px 5px 5px 15px;zoom:1;position:relative;width:100%;box-sizing: border-box;}.upload:after{display:block;clear:both;content:"";visibility:hidden;height:0}.upload .item{width:150px;height:120px;float:left;margin:0 10px 10px 0;background:#f8f8f8;position:relative;border-radius:4px;background-size:contain;background-position:center center;background-repeat:no-repeat;border:1px solid #d7d7d7;overflow:hidden}.upload .item .filename{font-size:12px;width:90%;left:5%;position:absolute;top:70%;line-height:1.3em;height:2.6em;overflow:hidden;text-align:center}.upload .item.error{border-color:#f20}.upload .item.error::after{content:"";background:rgba(255,255,255,.8);position:absolute;width:100%;height:100%;z-index:9;display:block;line-height:100%;text-align:center}.upload .item.error::before{content:attr(data-error);position:absolute;padding:10px;z-index:10;display:block;font-size:12px;color:#f20;top:0}.upload .item svg.icon{position:absolute;height:40%;top:20%;left:0;width:100%;opacity:0.6;}.upload .item svg.progress{position:absolute;bottom:0;width:100%;height:50%}.upload .item .progressnum{width:40px;height:40px;border-radius:40px;text-align:center;line-height:40px;font-size:12px;color:#fff;position:absolute;left:50%;margin-left:-20px;top:50%;margin-top:-20px;background:rgba(17,89,164,0.5)}.upload .item.add svg{top:30%}.upload .item.success::after{position:absolute;background:rgba(0,0,0,.6);content:"";left:0;right:0;top:0;bottom:0;opacity:0;transition:all .3s}.upload .item.success:hover::after{opacity:1}.upload .item.success svg.delete{position:absolute;height:30px;top:50%;margin-top:-15px;left:50%;color:#fff;z-index:10;transition:all .3s;cursor:pointer}.upload .item.success svg.delete{margin-left:-35px;height:26px;margin-top:-14px;left:-30px}.upload .item.success:hover svg.delete{margin-left:-15px;left:50%;height:26px;margin-top:-14px;transition:all .3s}.upload .item.delete{opacity:.2;transition:all .3s}.upload input[type=\'file\']{display:none}.upload.one{width:150px;height:150px;padding:0}.upload.one li{height:100%;width:100%;margin:0;padding:0}.upload.one li.add svg{opacity:0;transition:all .3s;}.upload.one li.add:hover svg{opacity:1;transition:all .3s}.upload.one li.add svg{opacity:0.6;transition:all .3s}.upload.one .item{border:0 none;border-radius:0}.upload.one .add{position:absolute;top:0;right:0;left:0;background:0;box-sizing: border-box;}.upload.one .item.success svg.delete{top:0;margin-top:5px}.upload.multiple .add::before{content:"最多上传 "attr(data-num) " 个文件";width:100%;text-align:center;position:absolute;bottom:5px;font-size:12px;margin-top:14px;left:0;color:#999;white-space:nowrap}';
            document.getElementsByTagName('head')[0].appendChild(styleElement);
        }
        var r = a(this),
                w = "<li class='item'></li>",
                A = a('<li class="item add"><svg class="icon" viewBox="0 0 1024 1024" version="1" xmlns="http://www.w3.org/2000/svg" width="200" height="200"><defs><style/></defs><path d="M768 810.7c-23.6 0-42.7-19.1-42.7-42.7s19.1-42.7 42.7-42.7c94.1 0 170.7-76.6 170.7-170.7 0-89.6-70.1-164.3-159.5-170.1L754 383l-10.7-22.7c-42.2-89.3-133-147-231.3-147s-189.1 57.7-231.3 147L270 383l-25.1 1.6c-89.5 5.8-159.5 80.5-159.5 170.1 0 94.1 76.6 170.7 170.7 170.7 23.6 0 42.7 19.1 42.7 42.7s-19.1 42.7-42.7 42.7c-141.2 0-256-114.8-256-256 0-126.1 92.5-232.5 214.7-252.4C274.8 195.7 388.9 128 512 128s237.2 67.7 297.3 174.2C931.5 322.1 1024 428.6 1024 554.7c0 141.1-114.8 256-256 256z" fill="#3688FF" p-id="4221"></path><path d="M640 789.3c-10.9 0-21.8-4.2-30.2-12.5L512 679l-97.8 97.8c-16.6 16.7-43.7 16.7-60.3 0-16.7-16.7-16.7-43.7 0-60.3l128-128c16.6-16.7 43.7-16.7 60.3 0l128 128c16.7 16.7 16.7 43.7 0 60.3-8.4 8.4-19.3 12.5-30.2 12.5z" fill="#5F6379" p-id="4222"></path><path d="M512 960c-23.6 0-42.7-19.1-42.7-42.7V618.7c0-23.6 19.1-42.7 42.7-42.7s42.7 19.1 42.7 42.7v298.7c0 23.5-19.1 42.6-42.7 42.6z" fill="#5F6379" p-id="4223"></path></svg></li>'),
                e = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="300" height="300" class="progress"><g fill="rgba(17,89,164,0.1)"><path d="M 0 70 Q 75 39, 150 70 T 300 70 T 450 70 T 600 70 T 750 70 V 320 H 0 V 0"></path><animateTransform attributeName="transform" attributeType="XML" type="translate" from="0" to="-300" dur="1.5s" repeatCount="indefinite"></animateTransform></g><g fill="rgba(17,89,164,0.15)"><path d="M 0 70 Q 87.5 47, 175 70 T 350 70 T 525 70 T 700 70 T 875 70 T 1050 70 V 320 H 0 V 0"></path><animateTransform attributeName="transform" attributeType="XML" type="translate" from="0" to="-350" dur="3s" repeatCount="indefinite"></animateTransform></g></svg><div class="progressnum"></div>',
                g = '<svg xmlns="http://www.w3.org/2000/svg" class="delete" version="1" viewBox="0 0 1024 1024"><path fill="#fff" d="M512 70a439 439 0 0 1 442 442 439 439 0 0 1-442 442A439 439 0 0 1 70 512 439 439 0 0 1 512 70m0-40a482 482 0 1 0 0 964 482 482 0 0 0 0-964zm114 253v-1c0-21-17-38-38-38H436c-21 0-38 17-38 38v1H282v74h460v-74H626zM321 396v346c0 21 17 38 38 38h306c21 0 38-17 38-38V396H321zm114 306h-76V474h76v228zm115 0h-76V474h76v228zm115 0h-76V474h76v228z"/></svg>',
                d = a('<input type="file" name="file" />'),
                b = a('<input type="hidden" />'),
                bname = a('<input type="hidden" />'),
                j, u, t, o, n, m, y, B = {
                    height: r.attr("data-height") ? r.attr("data-height") : 0, //压缩后最大高度，0自适应
                    width: r.attr("data-width") ? r.attr("data-width") : 800, //压缩后最大宽度，显示宽度
                    type: r.attr("data-type") ? r.attr("data-type") : "png,jpg,jpeg,gif", //上传文件后缀
                    jpg: r.attr("data-jpg") ? parseFloat(r.attr("data-jpg")) : 0.6, //jpg压缩比例，0不压缩
                    upname: r.attr("data-file") ? r.attr("data-file") : "file", //formdata name
                    save: r.attr("data-save") ? r.attr("data-save") : "upload/tmp/{Rnd}.{Ext}", //保存路径文件名
                    inputname: r.attr("data-name") ? r.attr("data-name") : "upload", //input name
                    num: r.attr("data-num") ? r.attr("data-num") : 1, //最多上传文件数量
                    nodel: r.attr("data-nodel")=='true' ? true : false, //是否自动删除已上传的文件
                    action: r.attr("action") ? r.attr("action") : "upload.php", //后端处理文件名
                    size: r.attr("data-size") ? r.attr("data-size") : 1024*1024*1024, //非图片文件尺寸限制
                    value: r.attr("data-value") ? r.attr("data-value") : "",
                    valuename: r.attr("data-valuename") ? r.attr("data-valuename") : ""
                };
        r.append(A.attr("data-num", B.num).attr("data-type", B.type));
        r.append(d.attr("multiple", B.num > 1 ? "multiple" : false));
        r.append(b.attr("name", B.inputname));
        r.append(bname.attr("name", B.inputname+"_name"));
        r.addClass(B.num > 1 ? "multiple" : "one");
        if(B.num < 1){
            B.num = 1;
            r.hide();
        }
        r.on("click", "li.add", function() {
            d.click();
        }).on("change", "input[type='file']", function(H) {
            var G = H.target.files;
            var i = [];
            for (var F in G) {
                if (typeof G[F] == "object") {
                    i.push(G[F])
                }
            }
            C(i)
        }).on("mouseenter", function(i) {
            B.paste = true;
        }).on("mouseleave", function(i) {
            B.paste = false;
        }).on("click", "li.error", function(i) {
            z(a(i.currentTarget))
        }).on("click", ".delete", function(i) {
            z(a(i.currentTarget).parent("li.success"))
        });
        if (B.value) {
            var p = B.value.split(","),pname = B.valuename.split(","),
                    s = 0,
                    h = "";
            var f = 0;
            for (var x in p) {
                if (s < B.num && p[s]) {
                    h = a("<li class='item success'></li>").append(k(p[s].toLowerCase().split(".").splice(-1).join())).append(g).append("<div class='filename'>"+pname[s]+"</div>").attr("data-url", p[s]).attr("data-uname", pname[s]).attr("data-filename", c(p[s])).css("background-image", "url('" + p[s] + "')").insertBefore(A);
                    r.data("num", ++f);//.removeClass("empty");
                    q()
                }
                s++
            }
        } else {
            r.data("num", 0);//.addClass("empty")
        }
        document.addEventListener('paste', function(e) {
            if (!B.paste)
                return;
            if (!event.clipboardData && !event.originalEvent)
                return console.log('不支持剪贴板粘贴');
            var clipboardData = (event.clipboardData || event.originalEvent.clipboardData);
            if (!clipboardData.items)
                return console.log('剪贴板粘贴无item');

            var len = clipboardData.items.length;
            var bb = [];
            for (var i = 0; i < len; i++) {
                if (clipboardData.items[i].type.indexOf("image") !== -1)
                {
                    var blob = clipboardData.items[i].getAsFile();
                    blob.name = 'clip' + Math.random() + '.jpg';
                    bb.push(blob);
                }
            }
            C(bb);
        });
        function C(H) {
            var G = H;
            for (var F in G) {
                if (typeof G[F] != "object")
                    continue;
                var i = l(G[F], function(K, J, I) {
                    J.insertBefore(A);
                    var filename = B.save;
                    var d = new Date();
                    filename = filename.replace(/{Y}/,d.getFullYear()).replace(/{M}/,d.getMonth()+1).replace(/{D}/,d.getDate()).replace(/{H}/,d.getHours()).replace(/{I}/,d.getMinutes()).replace(/{S}/,d.getSeconds());
                    filename = filename.replace(/{Rnd}/,d.getMilliseconds()+""+parseInt(Math.random()*1000));
                    //可扩展slice分片上传
                    var L = new FormData();
                    L.append(B.upname, K, I.name);
                    if (B.num == 1)
                        L.append('delfile', b.val());
                    //r.removeClass("empty");
                    a.ajax({
                        url: B.action + "?filepath=" + filename,
                        type: "POST",
                        data: L,
                        dataType: "text",
                        processData: false,
                        contentType: false,
                        success: function(M) {
                            M = JSON.parse(M);
                            J.children("svg.progress").remove();
                            J.children(".progressnum").remove();
                            if (M.result) {
                                J.addClass("success").append(g).data("url", M.msg).data("uname", M.name)
                            } else {
                                J.addClass("error").attr("data-error", M.msg ? M.msg : "服务端返回数据异常")
                            }
                            q();
                            if (D && typeof D == "function")
                                D(M.msg);
                        },
                        xhr: function() {
                            var M = new XMLHttpRequest();
                            M.upload.addEventListener("progress", function(N) {
                                if (N.lengthComputable) {
                                    var O = Math.round(N.loaded * 100 / N.total);
                                    J.children("svg.progress").css("height", 50 + (120 * O / 100) + "%");
                                    J.children(".progressnum").text(O + "%")
                                }
                            }, false);
                            return M
                        },
                        error: function() {
                            J.addClass("error").attr("data-error", "网络连接异常！");
                            q()
                        }
                    });
                });
            }
        }
        function l(G, L) {
            d.val("");
            if (B.num == 1) {
                r.children(".item").not(".add").remove()
            } else {
                if (B.num <= r.data("num")) {
                    return false
                }
                r.data("num", r.data("num") + 1)
            }
            var F = G.size,
                    J = G.name.toLowerCase().split(".").splice(-1).join(),
                    i = a(w);
            var I = B.type.split(",");
            if (I.indexOf(J) < 0) {
                var K = "不能上传." + J + "的文件!"
            }
            if (F > B.size) {
                var K = "不能上传大于." + B.size + "KB 的文件!"
            }
            if (K) {
                i.append(k(J)).addClass("error").append("<div class='filename'>" + G.name + "</div>").attr("data-error", K).insertBefore(A);
                //r.removeClass("empty");
                q();
                return false
            }
            var H = new FileReader();
            H.readAsDataURL(G);
            H.onload = function() {
                var P = c(G.name) + c(G.type) + c(G.size.toString()) + c(H.result);
                if (r.children('li[data-filename="' + P + '"]').size() > 0) {
                    return false
                }
                i.attr("data-filename", P);
                if (["png", "jpg", "jpeg", "gif", "bmp"].indexOf(J) >= 0) {
                    var O = new Image();
                    O.src = H.result;
                    O.onload = function() {
                        var V = this;
                        var X = V.width,
                                T = V.height,
                                S = X / T;
                        if (B.width && B.width < X) {
                            X = B.width;
                            T = X / S
                        } else {
                            if (B.height && T > B.height) {
                                T = B.height;
                                X = T * S
                            }
                        }
                        var Y = 1;
                        var Q = document.createElement("canvas");
                        var aa = Q.getContext("2d");
                        var W = document.createAttribute("width");
                        W.nodeValue = X;
                        var R = document.createAttribute("height");
                        R.nodeValue = T;
                        Q.setAttributeNode(W);
                        Q.setAttributeNode(R);
                        aa.drawImage(V, 0, 0, X, T);
                        if (["jpg", "jpeg"].indexOf(J) >= 0)
                            var U = Q.toDataURL("image/jpeg", (B.jpg == 0) ? 0.4 : B.jpg);
                        else
                            var U = Q.toDataURL();
                        i.css("background-image", "url('" + U + "')").append(e);
                        if (B.jpg > 0)
                        {
                            var Z = v(U);
                            L(Z, i, G);
                        }
                        else
                            L(G, i, G);
                        return
                    }
                } else {
                    var N = k(J);
                    i.append(N).append("<div class='filename'>" + G.name + "</div>").append(e);
                    var M = G;
                    L(M, i, G);
                    return
                }
            }
        }
        function v(G) {
            var i = G.split(","),
                    I = i[0].match(/:(.*?);/)[1],
                    F = atob(i[1]),
                    J = F.length,
                    H = new Uint8Array(J);
            while (J--) {
                H[J] = F.charCodeAt(J)
            }
            return new Blob([H], {
                type: I
            })
        }
        function c(F) {
            var J = 5381,
                    I = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-".split(""),
                    G = F.length - 1;
            if (typeof F == "string") {
                for (; G > -1; G--) {
                    J += (J << 5) + F.charCodeAt(G)
                }
            } else {
                for (; G > -1; G--) {
                    J += (J << 5) + F[G]
                }
            }
            var H = J & 2147483647;
            var K = "";
            do {
                K += I[H & 63]
            } while (H >>= 6);
            return K
        }
        function k(i) {
            if (["asp", "php", "js", "java", "html", "css", "sql"].indexOf(i) >= 0) {
                return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" version="1" viewBox="0 0 1024 1024"><defs/><path fill="#FF8976" d="M160 32a49 49 0 0 0-48 48v864c0 12 5 25 14 34 10 9 22 14 34 14h704c12 0 25-5 34-14 9-10 14-22 14-34V304L640 32H160z"/><path fill="#FFD0C8" d="M912 304H688c-12 0-25-5-34-14s-14-22-14-34V32l272 272z"/><path fill="#FFF" d="M422 564l-118 46 118 47v37l-163-65v-37l163-65v37zm116-106h37l-89 240h-37l89-240zm64 200l118-47-118-46v-37l163 64v38l-163 64v-36z"/></svg>'
            }
            if (["psb", "psd"].indexOf(i) >= 0) {
                return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" version="1" viewBox="0 0 1024 1024"><path fill="#8095FF" d="M168 32c-12 0-25 5-34 14s-14 22-14 34v864c0 12 5 25 14 34 10 9 22 14 34 14h704c12 0 25-5 34-14 9-10 14-22 14-34V304L648 32H168z"/><path fill="#CCD5FF" d="M920 304H696c-12 0-25-5-34-14s-14-22-14-34V32l272 272z"/><path fill="#C0CAFF" d="M504 550c5-2 10-4 16-4s11 2 16 4l185 108c5 2 8 8 8 13s-3 11-8 14L534 793c-4 2-10 4-16 4s-11-2-16-4L318 686c-5-3-8-8-8-14s3-11 8-14l186-108z"/><path fill="#FFF" d="M504 390c5-2 10-4 16-4s11 2 16 4l185 108c5 2 8 8 8 13s-3 11-8 14L534 633c-4 2-10 4-16 4s-11-2-16-4L318 526c-5-3-8-8-8-14s3-11 8-14l186-108z"/></svg>'
            }
            if (["xls", "xlsx", "number", "et", "ett"].indexOf(i) >= 0) {
                return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" version="1" viewBox="0 0 1024 1024"><path fill="#5ACC9B" d="M160 32a49 49 0 0 0-48 48v864c0 12 5 25 14 34 10 9 22 14 34 14h704c12 0 25-5 34-14 9-10 14-22 14-34V304L640 32H160z"/><path fill="#BDEBD7" d="M912 304H688c-12 0-25-5-34-14s-14-22-14-34V32l272 272z"/><path fill="#FFF" d="M475 538L366 386h76l71 108 74-108h73L549 538l117 161h-76l-79-115-78 116h-75l117-162z"/></svg>'
            }
            if (["wps", "wpt", "page", "doc", "docx"].indexOf(i) >= 0) {
                return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" version="1" viewBox="0 0 1024 1024"><path fill="#6CCBFF" d="M160 32a49 49 0 0 0-48 48v864c0 12 5 25 14 34 10 9 22 14 34 14h704c12 0 25-5 34-14 9-10 14-22 14-34V304L640 32H160z"/><path fill="#C4EAFF" d="M912 304H688c-12 0-25-5-34-14s-14-22-14-34V32l272 272z"/><path fill="#FFF" d="M280 386h65l65 244 72-244h62l72 244 66-244h62l-96 314h-65l-71-242h-1l-72 241h-65l-94-313z"/></svg>'
            }
            if (["ppt", "pptx", "key", "dps", "dpt", "wpp"].indexOf(i) >= 0) {
                return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" version="1" viewBox="0 0 1024 1024"><path fill="#FF8976" d="M160 32a49 49 0 0 0-48 48v864c0 12 5 25 14 34 10 9 22 14 34 14h704c12 0 25-5 34-14 9-10 14-22 14-34V304L640 32H160z"/><path fill="#FFD0C8" d="M912 304H688c-12 0-25-5-34-14s-14-22-14-34V32l272 272z"/><path fill="#FFF" d="M386 386h176c70 0 92 47 92 97 0 48-28 97-92 97H446v120h-60V386zm60 145h96c35 0 53-10 53-47 0-38-25-48-48-48H446v95z"/></svg>'
            }
            if (i == "pdf") {
                return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" version="1" viewBox="0 0 1024 1024"><path fill="#FF5562" d="M160 32a49 49 0 0 0-48 48v864c0 12 5 25 14 34 10 9 22 14 34 14h704c12 0 25-5 34-14 9-10 14-22 14-34V304L640 32H160z"/><path fill="#FFBBC0" d="M912 304H688c-12 0-25-5-34-14s-14-22-14-34V32l272 272z"/><path fill="#FFF" d="M696 843c-50 0-95-86-119-142-40-17-84-32-127-43-37 25-100 62-149 62-31 0-52-15-60-42-7-21-1-36 5-44 13-18 40-27 80-27 32 0 72 6 118 17 30-21 59-45 86-70-12-56-25-147 8-188 16-20 40-27 70-18 33 10 45 30 49 45 13 54-49 128-91 171 9 38 21 77 36 113 61 27 133 67 141 111 3 15-1 30-13 42-11 8-22 13-34 13zm-74-121c30 61 59 90 74 90 2 0 6-1 10-5 6-5 6-9 5-13-3-16-29-42-89-72zm-296-83c-40 0-51 10-54 14-1 1-4 5-1 17 3 9 9 19 30 19 25 0 62-15 105-40-31-7-58-10-80-10zm158-5c26 8 52 16 77 26-9-23-16-47-23-70l-54 44zm99-258c-9 0-15 3-21 10-16 20-18 73-5 140 49-52 75-100 69-125-1-4-4-15-27-22l-16-3z"/></svg>'
            }
            if (i == "txt") {
                return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" version="1" viewBox="0 0 1024 1024"><path fill="#E5E5E5" d="M160 32a49 49 0 0 0-48 48v864c0 12 5 25 14 34 10 9 22 14 34 14h704c12 0 25-5 34-14 9-10 14-22 14-34V304L640 32H160z"/><path fill="#CCC" d="M912 304H688c-12 0-25-5-34-14s-14-22-14-34V32l272 272z"/><path fill="#FFF" d="M264 376h208c14 0 24-10 24-24s-10-24-24-24H264c-14 0-24 10-24 24s10 24 24 24zm0 160h496c14 0 24-10 24-24s-10-24-24-24H264c-14 0-24 10-24 24s10 24 24 24zm496 112H264c-14 0-24 10-24 24s10 24 24 24h496c14 0 24-10 24-24s-10-24-24-24z"/></svg>'
            }
            if (["zip", "rar", "gzip", "7-zip", "zipz", "rarr", "iso"].indexOf(i) >= 0) {
                return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" version="1" viewBox="0 0 1024 1024"><path fill="#5ACC9B" d="M944 944H80c-26 0-48-18-48-41V656h960v247c0 23-22 41-48 41z"/><path fill="#6CCBFF" d="M80 80h864c26 0 48 18 48 41v247H32V121c0-23 22-41 48-41z"/><path fill="#FFD766" d="M32 368h960v288H32z"/><path fill="#FF5562" d="M352 80h320v864H352z"/><path fill="#FFF" d="M444 128h64v48h-64zm64-48h64v48h-64zm0 96h64v48h-64zm-64 48h64v48h-64zm64 48h64v48h-64zm-64 48h64v48h-64zm64 48h64v48h-64zm-64 48h64v48h-64zm64 48h64v48h-64zm-64 48h64v48h-64zm64 48h64v48h-64zm-64 48h64v48h-64zm64 48h64v48h-64zm-64 48h64v48h-64zm64 48h64v48h-64zm-64 48h64v48h-64zm0 96h64v48h-64zm64-48h64v48h-64z"/></svg>'
            }
            if (["avi", "wmv", "mpeg", "mp4", "mov", "mkv", "flv", "f4v", "m4v", "rmvb", "rm", "3gp", "dat", "ts", "mts", "vob"].indexOf(i) >= 0) {
                return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" version="1" viewBox="0 0 1024 1024"><path fill="#8095FF" d="M80 34h864v960H80z"/><path fill="#FFF" d="M136 112a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM136 272a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM136 432a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM136 592a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM136 752a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM136 912a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM824 112a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM824 272a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM824 432a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM824 592a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM824 752a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM824 912a40 40 0 1 0 80 0 40 40 0 1 0-80 0zM648 508L436 362c-5-3-11-4-17 0-5 3-9 8-9 14v290c0 6 4 12 9 15 6 3 12 2 17-1l212-146c5-3 7-8 7-13s-3-10-7-13z"/></svg>'
            }
            if (["gif", "jpg", "jpeg", "png", "bmp"].indexOf(i) >= 0) {
                return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" version="1" viewBox="0 0 1024 1024"><defs/><path fill="#FF5562" d="M160 32a49 49 0 0 0-48 48v864c0 12 5 25 14 34 10 9 22 14 34 14h704c12 0 25-5 34-14 9-10 14-22 14-34V304L640 32H160z"/><path fill="#FFBBC0" d="M912 304H688c-12 0-25-5-34-14s-14-22-14-34V32l272 272z"/><path fill="#FFF" d="M758 706L658 550c-3-4-8-7-13-7s-11 3-14 7l-53 84-120-195c-4-5-8-7-14-7s-10 3-14 7L266 706c-4 4-4 11 0 16 3 5 8 8 13 8h466c5 0 11-4 14-8 3-6 3-12-1-16zM622 412a40 40 0 1 0 80 0 40 40 0 1 0-80 0z"/></svg>'
            }
            return '<svg xmlns="http://www.w3.org/2000/svg" class="icon" version="1" viewBox="0 0 1024 1024"><path fill="#E5E5E5" d="M160 32a49 49 0 0 0-48 48v864c0 12 5 25 14 34 10 9 22 14 34 14h704c12 0 25-5 34-14 9-10 14-22 14-34V304L640 32H160z"/><path fill="#CCC" d="M912 304H688c-12 0-25-5-34-14s-14-22-14-34V32l272 272z"/></svg>'
        }
        function q() {
            var i = [];
            var iname = [];
            r.children("li.success").each(function() {
                i.push(a(this).data("url"));
                iname.push(a(this).data("uname"));
            });
            if (r.children("li").not(".add").size() >= B.num && B.num > 1) {
                A.hide()
            } else {
                A.show()
            }
            if (r.children("li").not(".add").size() == 0) {
                //r.addClass("empty")
            }
            var ii = [];
            if (b.val() != "")
                ii = b.val().split(",");
            if(!B.nodel)
            {
                for (var xx in ii)
                {
                    var bdel = true;
                    for (var x in i)
                    {
                        if (i[x] == ii[xx])
                            bdel = false;
                    }
                    if (bdel)
                        a.ajax({url: B.action + "?delfile=" + ii[xx]});
                }
            }
            b.val(i.join(","));
            bname.val(iname.join(","));
        }
        function z(i) {
            i.fadeOut(333, function() {
                i.remove();
                q()
            });
            r.data("num", r.data("num") - 1)
        }
    }
})(jQuery);