
                // Example using HTTP POST operation

"use strict";


//Тут объявляю несколько юзерагентов, типа мы под разными браузерами заходим постоянно
var useragent = [];
useragent.push('Opera/9.80 (X11; Linux x86_64; U; fr) Presto/2.9.168 Version/11.50');
useragent.push('Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25');
useragent.push('Opera/12.02 (Android 4.1; Linux; Opera Mobi/ADR-1111101157; U; en-US) Presto/2.9.201 Version/12.02');

//Здесь находится страничка, которую нужно спарсить
var parseUrl = '/volgogradskaya_oblast_volzhskiy/kvartiry/2-k_kvartira_43.6_m_55_et._979759640';
var title = '2-к квартира, 43.6 м², 5/5 эт.';
var job = {title: title, url: parseUrl, phone: "", address: "", city: "", price: "", category: "", title_obj: "", contact_name: "", desc : "", person_name : "", id : "", date: ""};                               
var jobs_list = [];
var debug = false;
var click_count = 0;
var arr_debug = [];
var click = false;
var page = require('webpage').create();

// Это я передаю заголовки
// Их можно посмотреть в браузере на закладке Network (тыкайте сами, ищите сами)
page.customHeaders = {
    ":host": "m.avito.ru",
    ":method": "GET",
    ":path": "/",
    ":scheme": "https",
    ":version": "HTTP/1.1",
    "accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
    "accept-language": "ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
    "cache-control": "max-age=0",
    "upgrade-insecure-requests": "1",
    "user-agent": useragent[Math.floor(Math.random() * useragent.length)]
};

// Здесь я отключаю загрузку сторонних скриптов для ускореняи парсинга
page.onResourceRequested = function (requestData, request) {
    if ((/http:\/\/.+?\.css$/gi).test(requestData['url'])) {
        request.abort();
    }
    if (
        (/\.doubleclick\./gi.test(requestData['url'])) ||
        (/\.pubmatic\.com$/gi.test(requestData['url'])) ||
        (/yandex/gi.test(requestData['url'])) ||
        (/google/gi.test(requestData['url'])) ||
        (/gstatic/gi.test(requestData['url']))
    ) {
        request.abort();
        return;
    }
};


//Этот код выводит ошибки, дебаг так сказать
page.onError = function (msg, trace) {
    console.log(msg);
    trace.forEach(function (item) {
        console.log('  ', item.file, ':', item.line);
    });
};

String.prototype.stripTags = function() {
    return this.replace(/<\/?[^>]+>/g, '');
};

function mouseclick( element ) {
    // create a mouse click event
    var event = document.createEvent( 'MouseEvents' );
    event.initMouseEvent( 'click', true, true, window, 1, 0, 0 );
    // send click to element
    element.dispatchEvent( event );
}

// final function called, output screenshot, exit
//noinspection JSAnnotator
function after_clicked( page, job ) {
        if (debug) {  
           arr_debug.push((new Date().getTime() - arr_debug[0]) + " late ms. this after");  
        }
            job.title_obj = page.evaluate(function() {
                return [].map.call(document.querySelectorAll('.semantic-text'), function (span) {
                    return span.innerText;
                });
            });
            job.desc = page.evaluate(function() {
                return document.querySelector('.description-preview-wrapper').innerText;
            });
            job.id = page.evaluate(function() {
                return document.querySelector('.item-id').innerText;
            });
            job.geo = page.evaluate(function() {
                var div_geo = document.querySelector('#item-map');
                if (div_geo !== null) {
                    var attr_1 = div_geo.getAttribute('data-coords-lat');
                    var attr_2 = div_geo.getAttribute('data-coords-lng');
                    return attr_1 + "," + attr_2;
                } else {
                    return "none";
                }
            });
            job.contact_name = page.evaluate(function() {
                var name = document.querySelector('.person-contact-name');
                if (name !== null) {
                    return name.innerText;
                } else {
                    return "none";
                }
            });
            job.person_name = page.evaluate(function() {
                var name = document.querySelector('.person-name');
                if (name !== null) {
                    return name.innerText;
                } else {
                    return "none";
                }
            });
            job.date = page.evaluate(function() {
                return document.querySelector('.item-add-date').innerText;
            });
            job.city = page.evaluate(function() {
                return document.querySelector('.avito-address-text').innerText;
            });
            job.category = page.evaluate(function() {
                return document.querySelector('.param-last').innerText;
            });
            job.address = page.evaluate(function() {
                return document.querySelector('.user-address-text').innerText;
            });
            job.phone = page.evaluate(function () {
                return document.querySelector('.action-show-number .js-phone-number').innerText;
            });
            job.price = page.evaluate(function () {
                return document.querySelector('.price-value').innerText;
            });
            console.log(JSON.stringify(job));
            if (debug) {
              for (var f = 0; f < arr_debug.length; f++) {
                    console.log(JSON.stringify(arr_debug[f]));
                }
            }
            phantom.exit( 1 );
}

function checkClick (page) {
    if(debug) {
       arr_debug.push((new Date().getTime() - arr_debug[0]) + " late ms. this check click");
    }
    click_count++;
     if (!click || click_count > 4) {
            var clicked = page.evaluate(
        function ( mouseclick_fn ) {
            // want the div with class "submenu"
            var element = document.querySelector( "a.action-show-number" );
            if ( ! element ) {
                return false;
            }
            // click on this inner div
            mouseclick_fn( element );
            return true;
        }, mouseclick
    );
    click = clicked;
     }
    if ( ! click ) {
        console.log( job.url);
        console.log( "Failed to find desired element" );
        phantom.exit( 1 );
        return;
        } else {
            var result =  page.evaluate(function() {
                var txt = document.querySelector( "a.action-show-number .js-phone-number" ).innerText;
                if (!txt.indexOf('XX-XX') + 1) {
                    return true;
                } else {
                    return false;
                }
            });
            return result;
    }
}

// middle function, click on desired tab
//noinspection JSAnnotator
function click_div( page, job ) {
        if (debug) {  
           arr_debug.push((new Date().getTime() - arr_debug[0]) + " late ms. this div click");  
        }
        waitFor(  function () {
                    return checkClick( page);
                },
                function () {
                    after_clicked( page, job );
                }, 7000);
}


function next_page(page, job) {
        if (debug) {
            arr_debug.push(new Date().getTime());  
           arr_debug.push(new Date().getTime() + " start parse ms.");  
        }
       page.open("https://m.avito.ru" + job.url, function (status) {
            if (status !== 'success') {
                console.log('Unable to access network');
            } else {
               click_div( page, job );
            }
        });
}


function doit(page, link, list_jobs) {
    // console.log( link );
    page.open(link, function (status) {
        if (status !== 'success') {
            console.log('Unable to access network');
        } else {
            var list = page.evaluate(function () {
                var job;
                var jobs = [];
                var objs = document.querySelectorAll('article.b-item');
                    for (var i = 0; i < objs.length; i++) {
                        var title = objs[i].querySelector('h3');
                        var url = objs[i].querySelector('a');
                        job = {title: title.innerText, url: url.getAttribute('href'), phone: "", address: "", city: "", price: "", category: "", title_obj: "", contact_name: "", desc : "", person_name : "", id : "", date : "", geo : ""};
                        jobs.push(job);
                    }
                return jobs;
            });
            // for (var f = 0; f < list.length; f++) {
            //     console.log(JSON.stringify(list[f]));
            // }
            // console.log("");
            var arre = list_jobs.concat(list);
            var next = page.evaluate(function () {
                return document.querySelector(".page-next a");
            });
            if (next !== "") {
                var href = page.evaluate(function () {
                    var next = document.querySelector(".page-next a");
                    return next.getAttribute('href');
                });
                href = "https://m.avito.ru" + href;
                window.setTimeout(
                    function () {
                        doit(page, href, arre);
                    },
                    1000
                );
            } else {
                var i = 0;
                window.setTimeout(function () {
                    next_page(i, page, arre);
                }, 3000);
            }
        }
    });
}

function waitFor(testFx, onReady, timeOutMillis) {
    var maxtimeOutMillis = timeOutMillis ? timeOutMillis : 3000, //< Default Max Timout is 3s
        start = new Date().getTime(),
        condition = false,
        interval = setInterval(function() {
            if ( (new Date().getTime() - start < maxtimeOutMillis) && !condition ) {
                // If not time-out yet and condition not yet fulfilled
                condition = (typeof(testFx) === "string" ? eval(testFx) : testFx()); //< defensive code
            } else {
                if(!condition) {
                    // If condition still not fulfilled (timeout but condition is 'false')
                    //console.log("'waitFor()' timeout");
                    phantom.exit(1);
                } else {
                    // Condition fulfilled (timeout and/or condition is 'true')
                    //console.log("'waitFor()' finished in " + (new Date().getTime() - start) + "ms.");
                    typeof(onReady) === "string" ? eval(onReady) : onReady(); //< Do what it's supposed to do once the condition is fulfilled
                    clearInterval(interval); //< Stop this interval
                }
            }
        }, 1500); //< repeat check every 250ms
};


next_page(page, job);
                