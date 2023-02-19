window.showToast = (type, message,) => {
    let toastEl = document.getElementById("toast");
    let msgEl = document.getElementById("toast-msg");
    msgEl.innerHTML = message;
    // console.log( );
    toastEl.classList.remove("bg-success", 'bg-danger');
    toastEl.classList.add("bg-" + type);
    let myToast = new bootstrap.Toast(toastEl, {delay: 4000});
    myToast.show();
};
window.showDialog = (type, message, onclick = null, params = null) => {

    // 0  ready for save
    // 1  success  save
    // else show errors
    if (type === 'confirm')
        swal.fire({
            title: "<h3 class='text-danger'>" + message + "</h3>",
            text: ' ',
            icon: 'error',
            showCancelButton: true,
            showCloseButton: true,
            cancelButtonText: 'انصراف',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'تایید',
        }).then((result) => {
            if (result.value) {
                onclick()(params);
            }
        });
    else if (type === 'pay')
        swal.fire({
            title: "<h3 class='text-danger'>" + message + "</h3>",
            text: ' ',
            icon: 'success',
            showCancelButton: true,
            showCloseButton: true,
            cancelButtonText: 'انصراف',
            confirmButtonColor: '#60aa2f',
            cancelButtonColor: '#d33',
            confirmButtonText: 'پرداخت',
        }).then((result) => {
            if (result.value) {
                onclick()(params);
            }
        });
    else if (type === 'success') {
        swal.fire({
            title: "<h3 class='text-success'>" + message + "</h3>",
            text: '',
            confirmButtonColor: '#60aa2f',
            icon: 'success',
            confirmButtonText: ' باشه',
        });

    } else if (type === 'success-flash') {
        swal.fire({
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500
        });

    } else {
        swal.fire({
            title: 'خطایی رخ داد',
            html: ` <p   class="text-danger">` + message + `</p>`,
//                        text: this.errors,
            confirmButtonColor: '#d33',
            icon: 'error',
            confirmButtonText: ' باشه',
        });
    }

};
window.countDownSMS = function countDownSMS(btn,) {
    let seconds = 60;
    let timer;
    let text = btn.innerHTML;

    function count() {

        if (seconds > 0) {
            seconds--;
            if (seconds < 10)
                btn.innerHTML = "0:0" + seconds;
            else
                btn.innerHTML = "0:" + seconds;

        } else {
            clearInterval(timer);
            btn.disabled = false;
            btn.innerHTML = text;
        }
    }

    if (!timer) {
        timer = window.setInterval(function () {
            count();
        }, 1000); // every second
    }
    btn.innerHTML = "1:00";
    btn.disabled = true;
    return timer;
};

window.addSMSBtnListener = function (name, family, phone) {


    //use my info checkbox
    let btnEl = document.querySelector('#phone_verify') || document.querySelector('#password');

    if (!btnEl) return; //there is not any sms form
    let smsVerifyEl = btnEl.parentElement;
    let checkbox = document.querySelector('#myinfo');
    let nameEl = document.querySelector('#name');
    let familyEl = document.querySelector('#family');
    let phoneEl = document.querySelector('#phone') || document.querySelector('#login');
    let smsBtnEl = document.querySelector('#phone_verify-addon1');

    if (checkbox)
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                if (name) {
                    nameEl.value = name;
                    nameEl.focus();
                }
                if (family) {
                    familyEl.value = family;
                    familyEl.focus();
                }
                if (phone) {
                    phoneEl.value = phone;
                    phoneEl.focus();
                }
                phoneEl.disabled = true;
                smsBtnEl.style.display = 'none';
                smsVerifyEl.style.display = 'none';
            } else {
                phoneEl.disabled = false;
                smsBtnEl.style.display = 'inline';
                smsVerifyEl.style.display = 'block';
            }
        });

    //get sms activation code

    if (!document.querySelector('#login') && (phoneEl.value === null || phoneEl.value === ""))
        phoneEl.value = "09";


    let btn = document.querySelector("#phone_verify-addon1");

    btn.onclick = function () {
        let timer = window.countDownSMS(btn);
        axios.post('/api/getactivationcode', {
            'phone': phoneEl.value
        })
            .then((response) => {
                    window.showToast(response.data.status, response.data.msg);
                    // console.log(response)
                }
            ).catch((error) => {
            window.showToast(error.data.status, error);
        });
    };


};

window.invalidInputs = function (data) {
    for (let d in data) {
        let el = document.getElementById(d);
        if (el && el.classList) {

            el.classList.add('is-invalid');
            el.classList.add('my-0');
        }
        let el2 = document.getElementById('err-' + d);

        if (el2) {
            el2.innerHTML = data[d][0];
        }

    }
    document.querySelector('#percent').innerHTML = "";

};
window.validInputs = function () {
    let data = document.querySelectorAll('.is-invalid');

    for (let d in data) {
        if (!data[d].classList)
            continue;
        data[d].classList.remove('is-invalid');
        // data[d].classList.add('my-1');
    }
    let data2 = document.querySelectorAll('[id^="err-"]');

    for (let d in data2) {
        if (data2[d].innerHTML)
            data2[d].innerHTML = '';

    }

};
import videojs from 'video.js';

window.initPlayer = function (src, height = null) {

    let player;
    let video = document.getElementById('video');
    let options = {
        controls: true,
        controlBar: true,
        autoplay: false,
        preload: 'auto'
    };
    if (height)
        options['height'] = height;

    player = videojs('my-video', options, (e) => {
//                    console.log('ready');
    });

    player.src({
        src: src, type: "video/mp4"
    });
    videojs.hook('beforeerror', function (player, err) {
        const error = player.error();

        if (err === null) {
            return error;
        }
        return {
            'code': 1,
            'message': 'فایل قابل پخش نیست.' + ' فرمت نامعتبر است و یا اینترنت متصل نیست'
        };
    });
    player.on('error', function (e) {
        document.querySelector('.vjs-error-display').addEventListener('click', function (e) {
            window.resetPlayer();

        });

    });
};
window.resetPlayer = function (src) {

    if (!this.player)
        this.initPlayer(src);
    else {
        this.player.reset();

        this.player.src({
            src: src ? src : this.doc, type: "video/mp4"
        });
        this.player.trigger('loadstart');
    }
};


import Swiper, {Navigation, SwiperSlide, Scrollbar, A11y, Pagination, Thumbs,} from 'swiper';

Swiper.use([Navigation, /*SwiperSlide, Scrollbar, A11y, Pagination,*/ Thumbs,]);
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';

window.initClubGallery = function (class1, class2) {
    let swiper = new Swiper(class1, {
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
    });
    let swiper2 = new Swiper(class2, {
        spaceBetween: 10,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        thumbs: {
            swiper: swiper,
        },
    });
};

window.f2e = function (str) {
    if (!str) return str;
    str = str.toString();
    let eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    let per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    for (let i in per) {
//                    str = str.replaceAll(eng[i], per[i]);
        let re = new RegExp(per[i], "g");
        str = str.replace(re, eng[i]);
    }
    return str;


};
window.e2f = function (str) {
    let eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    let per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    if (!str) return str;
    if (Array.isArray(str)) {
        for (let idx in str) {
            for (let i in per) {
//                    str = str.replaceAll(eng[i], per[i]);
                let re = new RegExp(eng[i], "g");
                str[idx] = str[idx].replace(re, per[i]);
            }
        }
        return str;
    }
    str = str.toString();

    for (let i in per) {
//                    str = str.replaceAll(eng[i], per[i]);
        let re = new RegExp(eng[i], "g");
        str = str.replace(re, per[i]);
    }
    return str;


};
import L from 'leaflet';
import 'leaflet.fullscreen';

window.leaflet = function (location, label, mode = 'view', src = null) {

    function getAddressFromLocation(latLon) {

        if (!latLon) return null;

        if (!latLon.lat) return null;
        axios.get("https://nominatim.openstreetmap.org/reverse", {
            params: {
                lat: latLon.lat,
                lon: latLon.lng,
                format: "json",
                'accept-language': 'fa'
            }
        }).then((response) => {

            if (response.status !== 200) return;
            // let addressEl = document.getElementById('address');
            if (response.data && response.data.display_name)

                map.eachLayer((layer) => {
                    if (layer instanceof L.Marker)
                        layer.bindPopup(`<small>${response.data.display_name}</small>`);
                    // layer.openPopup();
                });
        });
    }


    window.map = src;
    let marker;
    if (mode === 'removemarker') {

        map.eachLayer((layer) => {
            if (layer instanceof L.Marker)
                layer.remove();
        });

        return map;
    }
    if (mode === 'getLocation') {
        let res = null;
        map.eachLayer((layer) => {
            if (layer instanceof L.Marker) {
                res = layer.getLatLng().lat + "," + layer.getLatLng().lng;
                return false;
            }
        });

        return res;
    }
    document.getElementById("map").style.height = "12rem";

    function onMapClick(e) {

        if (marker) {
            if (!marker._map) {
                marker.addTo(map);
            }

        } else {
            marker = L.marker(e.latlng, {icon: myIcon}).addTo(map);
            marker.dragging.enable();
        }
        marker.setLatLng(e.latlng);
        getAddressFromLocation(e.latlng);
        // L.popup()
        //     .setLatLng(e.latlng)
        //     .setContent("You clicked the map at " + e.latlng.toString())
        //     .openOn(map);
    }

    let loc;
    let iranLoc = [32.4279, 53.6880,];
    loc = (location && location.split(',').length === 2 && location.split(',')[0] !== 'undefined') ? [parseFloat(location.split(',')[0]), parseFloat(location.split(',')[1])] : iranLoc;
    // loc = iranLoc;

    let mapLayers =
        {
            google: "https://mt.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&s=IR&hl=fa",
            osm: "http://{s}.tile.osm.org/{z}/{x}/{y}.png",

        };

    window.map = new L.map('map', {
        fullscreenControl: true,
        fullscreenControlOptions: {
            position: 'topleft',
            pseudoFullscreen: true,
            title: 'تمام صفحه',

        }
    }).setView(loc, location ? 16 : 8);
    // L.control.scale().addTo(map);

    L.tileLayer(mapLayers.google, {
        // attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        // maxZoom: 18,
        id: 'mapbox/streets-v11',
        // tileSize: 512,
        maxNativeZoom: 19, // OSM max available zoom is at 19.
        maxZoom: 22, // Match the map maxZoom, or leave map.options.maxZoom undefined.
        // zoomOffset: -1,
        accessToken: 'your.mapbox.access.token'
    }).addTo(map);
    const myIcon = L.icon({
        iconUrl: '/images/marker-icon.png',
        shadowUrl: '/images/marker-shadow.png',
        // ...
    });
    marker = L.marker(loc, {icon: myIcon}).addTo(map);

    //add clear button
    if (mode !== 'view') {
        let clearBtn = L.control({
            position: 'topleft',
            title: 'پاک کردن',
        });

        clearBtn.onAdd = function (src) {

            this._div = L.DomUtil.create('div', 'leaflet-touch  leaflet-bar ');
            let el = `<a class="d-block  hoverable-dark "    title="پاک کردن"   ><i class="fa  fa-times w-100 h-100 p-2" aria-hidden="true"></i></a>`;

            this._div.innerHTML = el;
            this._div.onclick = function (e) {
                e.stopPropagation();

                leaflet('close', null, 'removemarker', map)
            };
            return this._div;

        };
        clearBtn.addTo(map);
    }

    if (label)
        marker.bindPopup(`<small>${label}</small>`)/*.openPopup()*/;
    map.on('click', onMapClick);

    if (mode === 'edit') {
        marker.dragging.enable();
    }
    if (mode === 'create') {
    }

    document.querySelector('.leaflet-control-zoom-fullscreen').style.backgroundImage = "url('/images/vendor/leaflet.fullscreen/icon-fullscreen.svg')";
    return map;
};
document.addEventListener("DOMContentLoaded", function (event) {
    // !function () {
    //     function t() {
    //         var t = document.createElement("script");
    //         t.type = "text/javascript", t.async = !0, localStorage.getItem("rayToken") ? t.src = "https://app.raychat.io/scripts/js/" + o + "?rid=" + localStorage.getItem("rayToken") + "&href=" + window.location.href : t.src = "https://app.raychat.io/scripts/js/" + o + "?href=" + window.location.href;
    //         var e = document.getElementsByTagName("script")[0];
    //         e.parentNode.insertBefore(t, e)
    //     }
    //
    //     var e = document, a = window, o = process.env.MIX_RAYCHAT_API;
    //     "complete" == e.readyState ? t() : a.attachEvent ? a.attachEvent("onload", t) : a.addEventListener("load", t, !1)
    // }();

// <!---start GOFTINO code--->

    !function () {
        var i = "AkXURK", a = window, d = document;

        function g() {
            var g = d.createElement("script"), s = "https://www.goftino.com/widget/" + i,
                l = localStorage.getItem("goftino_" + i);
            g.async = !0, g.src = l ? s + "?o=" + l : s;
            d.getElementsByTagName("head")[0].appendChild(g);
        }

        "complete" === d.readyState ? g() : a.attachEvent ? a.attachEvent("onload", g) : a.addEventListener("load", g, !1);
    }();

    // <!---end GOFTINO code--->
});

window.calculateCoupon = function calculateCoupon(event, params,) {

    document.querySelector('#loading').classList.remove('d-none');
//                event.preventDefault();
    validInputs();
    axios.post("/coupon/calculate", params, {})
        .then((response) => {
//                        console.log(response);
                document.querySelector('#loading').classList.add('d-none');
                if (response.status === 200) {
                    for (let i in response.data) {
                        let el = document.getElementById(i + '-label');
                        if (el)
                            el.innerHTML = response.data[i] + ' تومان ';
                    }
                }
            }
        ).catch((error) => {

        document.querySelector('#loading').classList.add('d-none');
        let errors = '';
        invalidInputs(error.response.data.errors);

        if (error.response && error.response.status === 422)
            for (let idx in error.response.data.errors)
                errors += error.response.data.errors[idx] + '<br>';
        else {
            errors = error;
        }
        window.showToast('danger', errors);
    });
};

window.makePayment = function makePayment(event, data) {

    document.querySelector('#loading').classList.remove('d-none');
//                event.preventDefault();

    axios.post("/payment/create", data, {})
        .then((response) => {

                document.querySelector('#loading').classList.add('d-none');
                if (response.status === 200)
                    window.location = response.data.url;
            }
        ).catch((error) => {
        document.querySelector('#loading').classList.add('d-none');

        let errors = '';
        invalidInputs(error.response.data.errors);
        if (error.response && error.response.status === 422)
            for (let idx in error.response.data.errors)
                errors += error.response.data.errors[idx] + '<br>';
        else {
            errors = error;
        }
        window.showToast('danger', errors);
    });
}
