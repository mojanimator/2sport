<template>

    <div class="   " :key="componentKey">

        <div class="  w-100">
            <!--<label :for="id"-->
            <!--class="col-12 col-form-label text-center">  </label>-->
            <div :style="'height:'+height+'rem ' "
                 class="  uploader-container     " :id="'videocontainer-' + this.id"
                 style="border:dashed"
                 role="form" @mouseover="uploader.classList.add('hover');"
                 @dragover.prevent="uploader.classList.add('hover');"
                 @dragleave.prevent="uploader.removeClass('hover');"
                 @mouseleave=" uploader.classList.remove('hover');"
                 @drop.prevent="uploader.classList.remove('hover');filePreview($event  ) "
                 @click="openFileChooser($event,id+'-file')">

                <div v-show="!doc">
                    <div class=" p-2  small text-center ">
                        {{ label }}
                        <div class="text-center text-dark ">فرمت .mp4</div>
                    </div>
                </div>
                <div v-show="doc" class="text-center">
                    <i class="fa fa-video fa-2x" aria-hidden="true"></i>
                    <div>{{ doc ? doc.name : '' }}</div>
                </div>
                <!--" accept=".mp4, .avi, .3gp" -->
                <input v-show="false" :id="id+'-file'" class="col-12    " accept=".mp4" type="file"
                       :name="id+'-file'" @input="filePreview($event )"/>

            </div>
            <div v-show="doc" class="    rounded-lg    ">

                <video :style="'max-height:'+this.height+'rem' "
                       id="my-video"
                       class="video-js w-100  "
                       controls
                       preload="auto"
                       :poster="cover"

                >
                    <!--<source :src="preload" type="video/mp4"/>-->
                    <!--<source src="MY_VIDEO.webm" type="video/webm"/>-->
                    <p class="vjs-no-js">
                        مرورگر قادر به اجرای این ویدیو نیست
                        <!--<a href="https://videojs.com/html5-video-support/" target="_blank"-->
                        <!--&gt;بیشتر</a>-->

                    </p>
                </video>

                <div class="btn-group my-1 w-100 text-center " role="group" dir="rtl">
                    <div v-if="mode=='edit'" class="btn p-2 bg-danger text-white m-0"
                         title="حذف ویدیو" data-bs-toggle="tooltip" data-bs-placement="bottom"
                         @click="required? refresh(): showDialog('confirm', 'از حذف اطمینان دارید؟', onclick =   removeFile);">
                        <i class="fa fa-window-close text-white" aria-hidden="true"></i>
                    </div>
                    <div v-if="mode=='edit' && !before" class="btn p-2 bg-success text-white m-0"
                         title="آپلود ویدیو" data-bs-toggle="tooltip" data-bs-placement="bottom"
                         @click="uploadFile()">
                        <i class="fa fa-upload text-white" aria-hidden="true"></i>
                    </div>

                    <div v-if="mode!='edit'" class="btn btn-block p-2 bg-danger text-white m-0"
                         title="حذف ویدیو" data-bs-toggle="tooltip" data-bs-placement="bottom"
                         @click="refresh()">
                        <i class="fa fa-2x fa-recycle text-white" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

        </div>


        <div class=" vjs-icon-fullscreen-enter row col-12 ">


            <!--cropper and qr canvas-->
            <!--<canvas id="myCanvas"></canvas>-->
            <!--<input id="x" name="x" type="hidden"/>-->
            <!--<input id="y" name="y" type="hidden"/>-->
            <!--<input id="width" name="width" type="hidden"/>-->
            <!--<input id="height" name="height" type="hidden"/>-->


        </div>

    </div>


</template>

<script>


let doc = null;
let video = null;
let input = null;

let self;
let canvas;


//    import Cropper from 'cropperjs';

import videojs from 'video.js'


export default {


    props: ['link', 'id', 'height', 'preload', 'required', 'label', 'mode', 'cover', 'forId', 'callback', 'images', 'limit', 'cropRatio'],
    components: {},
    data() {
        return {
            componentKey: 1,
            star: null,
            doc: this.preload,
            before: this.preload,

            player: null,
            reader: null,
            loading: false,


            creating: false,
            removing: false,
            uploader: null,
            duration: null,

            errors: "",
        }
    },
    watch: {
        doc: function (val) {
//                console.log('val');
            if (val) {
//                    this.initCropper();
            }
        },
        loading: function (val) {
//                console.log('val');
            if (val) {
                document.querySelector('#loading').classList.remove('d-none');
            } else
                document.querySelector('#loading').classList.add('d-none');
        },
    },
    beforeDestroy() {
//            console.log("beforeDestroy")
    },
    computed: {
//            get_noe_faza: () => {
//                return Vue.noe_faza;
//            }
    },
    mounted() {
        self = this;
        video = document.getElementById('video-' + this.id);
//            console.log(image);
//            $(".point-sw")

        this.uploader = document.querySelector('#' + this.id + '-file');
        this.uploadContainer = document.querySelector('#videocontainer-' + this.id);

        if (this.preload)
            this.uploadContainer.classList.add('d-none');
        if (this.mode === 'edit')
            this.initPlayer();
    }
    ,
    beforeDestroy() {
        if (this.player) {
            this.player.dispose()
        }
    },
    created() {

    }
    ,
    updated() {


    },
    beforeUpdate() {
    }
    ,
    methods: {
        isFloat(num) {
            return Number(num) === num && num % 1 !== 0;
        },
        getDuration() {
            if (this.isFloat(this.duration))
                return Math.ceil(this.duration);
            return null;
        },
        showDialog(type, message, click) {
            window.showDialog(type, message, onclick = () => click);
        },
        initPlayer(src) {


            this.player = videojs('my-video', {
                controls: true,
                controlBar: true,
                autoplay: false,
                preload: 'auto'
            }, (e) => {
//                    console.log('ready');
            });

            this.player.src({
                src: src ? src : this.doc, type: "video/mp4"
            });

            videojs.hook('beforeerror', function (player, err) {
//                    const error = player.error();

                if (err === null) {
                    return null /*error*/;
                }

                return {
                    'code': 1,
                    'message': 'فایل قابل پخش نیست.' + ' فرمت نامعتبر است و یا اینترنت متصل نیست'
                };
            });

            this.player.on('error', function (e) {

                document.querySelector('.vjs-error-display').addEventListener('click', function (e) {
                    self.refresh();

                });
//
            });
            this.player.on('loadedmetadata', function (e) {
                self.duration = this.duration();
                self.getDuration();
            });
        },
        resetPlayer(src) {

            if (!this.player)
                this.initPlayer(src);
            else {
                this.player.reset();

//                this.player.pause().currentTime(0).trigger('loadstart');

//                this.player.bigPlayButton.show();
                this.player.src({
                    src: src ? src : this.doc, type: "video/mp4"
                });
                this.player.trigger('loadstart');
            }
        }
        ,

        removeFile() {

            axios.post(this.link, {
                'id': this.forId,
                'cmnd': 'delete-vid',
                'type': this.type,
            },)
                .then((response) => {
//                        console.log(response);
                    this.loading = false;
                    if (response.status === 200)
                        window.location.reload();
                    else {
                        window.showToast('danger', response, onclick = null);

                    }
                }).catch((error) => {

                this.loading = false;
                if (error.response && error.response.status === 422)
                    for (let idx in error.response.data.errors)
                        this.errors += error.response.data.errors[idx] + '<br>';
                else {
                    this.errors = error;
                }
                window.showToast('danger', this.errors, onclick = null);
            });
        }
        ,

        async uploadFile() {
//                console.log(this.$refs.dropdown.selected.length);

            this.loading = true;
            let fd = new FormData();
//                this.cropper.crop();
            fd.append('video', this.doc);
            fd.append('type', this.type);
            fd.append('data_id', this.id);
            fd.append('id', this.forId);
            fd.append('replace', (!!this.required) || this.preload);
            fd.append('cmnd', 'upload-vid');


            axios.post(this.link, fd,)
                .then((response) => {
                    console.log(response);
                    this.loading = false;
                    if (response.status === 200) {
                        window.location.reload();
                    } else {
                        window.showToast('danger', response.data, onclick = null);

                    }

                }).catch((error) => {
                this.loading = false;
                if (error.response && error.response.status === 422)
                    for (let idx in error.response.data.errors)
                        this.errors += error.response.data.errors[idx] + '<br>';
                else {
                    this.errors = error;
                }
                window.showToast('danger', this.errors, onclick = null);
            });


        }
        ,

        refresh() {
            this.uploadContainer.classList.remove('d-none');

            this.player.pause();
            this.uploader.files = null;
            this.uploader.value = null;
            doc = null;
            this.doc = null;
            this.before = null;
        }
        ,


        openFileChooser(event, from) {
//                send fake click for browser file
            let file_input = document.getElementById(from);
            if (document.createEvent) {
                let evt = document.createEvent("MouseEvents");
                evt.initEvent("click", false, true);
                file_input.dispatchEvent(evt);

            } else {
                let evt = new Event("click", {"bubbles": false, "cancelable": true});
                file_input.dispatchEvent(evt);
            }
        }
        ,
        filePreview(event) {
            let file;
//                console.log(e);
            if (event.dataTransfer) {
                file = event.dataTransfer.files[0];
            } else if (event.target.files) {
                file = event.target.files[0];
            }
            this.doc = file;
            this.uploadContainer.classList.add('d-none');

            this.resetPlayer(URL.createObjectURL(file));
//                event.target.value = '';

//                        console.log(files.length);
//                this.reader = new FileReader();
//                this.reader.onload = function (e) {
//
//                    doc = e.target.result;
//                    self.doc = doc;
//                    self.loading = false;
//
//                };
//                this.reader.readAsDataURL(file);
//                this.loading = true;


        }

        ,


    }
}


</script>

<style type="text/css" lang="scss">
/*@import "~videojs-font/dist";*/
@import "~video.js/dist/video-js.min.css";

$color: #6f42c1;
.uploader-container {
    display: flex;
    justify-content: center;
    vertical-align: middle;
    align-items: center;
    text-align: right;
    /*min-height: 8rem;*/
    &:hover, &.hover {
        color: rgba($color, 20%);
        cursor: pointer;
    }

}

.vjs-error-display {
    &:before {
        content: '↺' !important;

    }

    &:hover {
        cursor: pointer !important;
    }

}

.video-js .vjs-play-control.vjs-playing .vjs-icon-placeholder:before, .vjs-icon-pause:before {
    content: "\f103";
    font-family: 'VideoJS';
}

.video-js .vjs-mute-control .vjs-icon-placeholder:before, .vjs-icon-volume-high:before {
    content: "\f107";
    font-family: 'VideoJS';
}

.video-js .vjs-big-play-button .vjs-icon-placeholder:before, .video-js .vjs-play-control .vjs-icon-placeholder:before, .vjs-icon-play:before {
    content: "\f101";
    font-family: 'VideoJS';
}

.video-js .vjs-picture-in-picture-control .vjs-icon-placeholder:before, .vjs-icon-picture-in-picture-enter:before {
    content: "\f121";
    font-family: 'VideoJS';
}

.video-js .vjs-fullscreen-control .vjs-icon-placeholder:before, .vjs-icon-fullscreen-enter:before {
    content: "\f108";
    font-family: 'VideoJS';
}

div.vjs-icon-fullscreen-enter {
    display: none;
}
</style>
