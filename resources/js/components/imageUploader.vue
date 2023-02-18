<template>

    <div class="   ">

        <div class="   w-100   ">

            <label :for="id"
                   class=" text-center d-none"> </label>
            <!--<div :style="'width:'+(cropRatio*height)+'rem;height:'+height+'rem'"-->
            <div :style="'height:'+height+'rem; ' "
                 class="  uploader-container w-100  " :id="'container-' + this.id"
                 style="border:dashed"
                 role="form" @mouseover="uploader.classList.add('hover');"
                 @dragover.prevent="uploader.classList.add('hover');"
                 @dragleave.prevent="uploader.classList.remove('hover');"
                 @mouseleave=" uploader.classList.remove('hover');"
                 @drop.prevent="uploader.classList.remove('hover');filePreview($event  ) "
                 @click="openFileChooser($event,id+'-file')">

                <div>
                    <div class=" p-2  small text-center ">
                        {{label}}
                    </div>
                    <div class="text-center text-dark ">فرمت  .jpg</div>
                </div>

                <input v-show="false" :id="id+'-file'" class="col-12    " accept=".png, .jpg, .jpeg" type="file"
                       :key="id"
                       :name="id+'-file'" @input="  filePreview($event,id )"/>
                <input :id="id" class="col-12" :name="id" type="hidden"/>


            </div>
            <div v-show="doc" class="    rounded-lg  w-100">
                <img v-show="doc" :id="'img-'+id" class=" mw-100     "
                     @error="errorImage()"
                     @load="  uploadContainer.classList.add('d-none');initCropper()"
                     :src="doc"
                     alt=""/>
                <div class="btn-group my-1 w-100  " role="group" dir="rtl">
                    <div v-if="mode=='edit'" class="btn p-2 bg-danger text-white m-0"
                         title="حذف تصویر" data-bs-toggle="tooltip" data-bs-placement="bottom"
                         @click="required? refresh(): showDialog('confirm', 'از حذف اطمینان دارید؟', onclick =   removeImage);">
                        <i class="fa fa-window-close text-white" aria-hidden="true"></i>
                    </div>
                    <div v-if="mode=='edit'" class="btn p-2 bg-success text-white m-0"
                         title="آپلود تصویر" data-bs-toggle="tooltip" data-bs-placement="bottom"
                         @click="uploadImage()">
                        <i class="fa fa-upload text-white" aria-hidden="true"></i>
                    </div>
                    <div v-if="mode!='edit'" class="btn btn-block p-2 bg-success text-white m-0"
                         title="برش تصویر" data-bs-toggle="tooltip" data-bs-placement="bottom"
                         @click="cropImage()">
                        <i class="fa fa-2x fa-crop-alt text-white" aria-hidden="true"></i>
                    </div>
                    <div v-if="mode!='edit'" class="btn btn-block p-2 bg-danger text-white m-0"
                         title="حذف تصویر" data-bs-toggle="tooltip" data-bs-placement="bottom"
                         @click="refresh()">
                        <i class="fa fa-2x fa-recycle text-white" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

        </div>


        <div class="  row col-12 ">


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


    let input = null;

    let self;
    let canvas;


    import Cropper from 'cropperjs';


    export default {


        props: ['link', 'id', 'type', 'height', 'required', 'preload', 'label', 'mode', 'forId', 'callback', 'images', 'limit', 'cropRatio'],
        components: {},
        data() {
            return {
                componentKey: 1,
                star: null,
                doc: this.preload,
                reader: null,
                cropper: null,

                loading: false,


                creating: false,
                removing: false,
                uploader: null,

                errors: "",
            }
        },
        watch: {
            doc: function (val) {
//                console.log(val);
                if (val) {
//                    this.initCropper();

                } else {

                }
            },
            loading: function (val) {
//                console.log(val);
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
            this.image = document.getElementById('img-' + this.id);
//            console.log(image);
//            $(".point-sw")

            this.uploader = document.querySelector('#' + this.id + '-file');
            this.uploadContainer = document.querySelector('#container-' + this.id);

        }
        ,
        created() {

        }
        ,
        updated() {


            if (!this.creating) {
                this.initCropper();
//                console.log('update');
            }
//            this.AwesomeQRCode = AwesomeQRCode;
//            console.log(window.AwesomeQRCode)
        },
        beforeUpdate() {
        }
        ,
        methods: {
            showDialog(type, message, click) {
                window.showDialog(type, message, onclick = () => click);
            },
            errorImage() {
                this.doc = null;
                this.uploadContainer.classList.remove('d-none');

            },
            getCroppedData() { //input id=img value=cropped data
                //not upload file
                document.querySelector("#" + this.id + '-file').value = null;
                if (!this.cropper) return null;
                let c = this.cropper.getCroppedCanvas();
                if (c) {
                    document.querySelector('#' + this.id).value = c.toDataURL();
                    return document.querySelector('#' + this.id).value;
                }
                return null

            },
            refresh() {

                this.uploadContainer.classList.remove('d-none');
                this.image.src = null;
//                document.getElementById('img').value = null;
//                this.cropper.destroy();
//                this.componentKey++;
//                this.$forceUpdate();

                this.creating = false;
                this.initCropper();

            },
            cropImage() {
                this.loading = true;

                this.cropper.crop();
                this.loading = false;
                let img = this.cropper.getCroppedCanvas().toDataURL();

                if (this.mode === 'multi') {
                    if (this.images.length >= this.limit) {
                        window.showToast('danger', 'تعداد تصاویر بیش از حد مجاز است', onclick = null);
                        return;
                    }
                    this.images.push({id: this.images.length, src: img});
                    this.doc = null;

                    this.initCropper();
                } else {
                    document.querySelector('#' + self.id).value = img;
                    window.showToast('success', 'تصویر آماده ارسال است', onclick = null);
                }
//                this.cropper.getCroppedCanvas().toBlob((blob) => {
//                    this.loading = false;
//                    if (blob) {
//
//
//                        $('#' + self.id).val(blob);
//
//                        window.showDialog('success', 'تصویر آماده ارسال است', onclick = null);
//                    }
//
//
//                });

            },
            removeImage() {

                axios.post(this.link, {
                    'id': this.forId,
                    'cmnd': 'delete-img',
                    'type': this.type,
                    'data_id': this.id,
                    'replace': !!this.required,
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

            async uploadImage() {
//                console.log(this.$refs.dropdown.selected.length);

                this.loading = true;

//                this.cropper.crop();


                let fd = {
                    'img': this.cropper.getCroppedCanvas().toDataURL(),
                    'type': this.type,
                    'data_id': this.id,
                    'id': this.forId,
                    'replace': (!!this.required) || this.preload, //if true : replace with before image
                    'cmnd': 'upload-img',
                };

                axios.post(this.link, fd,)
                    .then((response) => {
//                        console.log(response);
                        this.loading = false;
                        if (response.status === 200) {
                            window.location.reload();
                        }
                        else {
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


            },

            initCropper() {

//
//                Cropper.noConflict();
                if (this.cropper)
                    this.cropper.destroy();

                this.cropper = new Cropper(this.image, {
//                    autoCrop: false,
                    autoCropArea: 1,
                    viewMode: 3, responsive: false,
//                    autoCrop: true,
                    style: {height: '100%', width: '100%'},
                    aspectRatio: this.cropRatio,
                    highlight: true,
                    restore: true,
                    cropBoxMovable: true,
                    zoomable: true,
//                    dragMode: 'move',
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    crop(event) {
//                        console.log('crop');
                        this.creating = true;

                    },
                    cropend(event) {
//                        console.log('croped');
                    },
                    ready(e) {

//                        if (self.mode !== 'edit') {
                        this.cropper.crop();

                        document.querySelector('#' + this.id).value = this.cropper.getCroppedCanvas().toDataURL();
//                        }

                        // this.cropper[method](argument1, , argument2, ..., argumentN);
//                        this.cropper.move(1, -1);

                        // Allows chain composition
//                        this.cropper.move(1, -1).rotate(45).scale(1, -1);
                    },
                });


            }
            ,

            openFileChooser(event, from) {
//                send fake click for browser file
                let image_input = document.getElementById(from);
                if (document.createEvent) {
                    let evt = document.createEvent("MouseEvents");
                    evt.initEvent("click", false, true);
                    image_input.dispatchEvent(evt);

                } else {
                    let evt = new Event("click", {"bubbles": false, "cancelable": true});
                    image_input.dispatchEvent(evt);
                }
            }
            ,
            filePreview(event, id) {
                let file;


                if (event.dataTransfer) {
                    file = event.dataTransfer.files[0];
                }
                else if (event.target.files) {
                    file = event.target.files[0];
                }
                event.target.value = '';
//                    console.log(files.length);
//                        console.log(files.length);
//                this.reader = new FileReader();
//                this.reader = new FileReader();

//                this.reader.onload = (e,  ) => {
////
//                    console.log(e);
//                    self.doc = e.target.result;
//
//                    self.loading = false;
//                    self.creating = false;
//
//                    self.uploadContainer.classList.add('d-none');
//                };

//                this.reader.readAsDataURL(file);

                this.doc = URL.createObjectURL(file);
                this.creating = false;
                this.$forceUpdate();
                this.uploadContainer.classList.add('d-none');


            },

            log(s) {
                console.log(s);
            }


        }
    }


</script>

<style type="text/css" lang="scss">
    @import "~cropperjs/dist/cropper.css";

    $color: #6f42c1;
    .uploader-container {
        /*display: flex;*/
        /*justify-content: center;*/
        /*vertical-align: middle;*/
        /*align-items: center;*/
        /*text-align: right;*/
        /*min-height: 8rem;*/
        &:hover, &.hover {
            color: rgba($color, 20%);
            cursor: pointer;
        }

    }
</style>
