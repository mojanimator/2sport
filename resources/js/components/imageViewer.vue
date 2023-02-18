<template>

    <div class="   ">

        <div class="   w-100   " :style="'height:'+height   ">
            <label :for="id"
                   class=" text-center"> </label>
            <!--<div :style="'width:'+(cropRatio*height)+'rem;height:'+height+'rem'"-->
            <div
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
                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    </div>

                </div>

                <input v-show="false" :id="id+'-file'" class="col-12    " accept=".png, .jpg, .jpeg" type="file"
                       :key="id"
                       :name="id+'-file'" @input="  filePreview($event,id )"/>
                <input :id="id" class="col-12" :name="id" type="hidden"/>


            </div>
            <div v-show="doc" class="position-relative    rounded-lg  w-100">
                <img v-show="doc" :id="'img-'+id" class="  h-100     "
                     @error="errorImage()"
                     @load="  uploadContainer.classList.add('d-none'); "
                     :src="doc"
                     alt=""/>
                <div class="position-absolute end-0 top-0  " role="group" dir="rtl">
                    <div class="btn p-2 bg-danger text-white m-0"
                         title="حذف تصویر" data-bs-toggle="tooltip" data-bs-placement="bottom"
                         @click=" refresh() ">
                        <i class="fa fa-window-close text-white" aria-hidden="true"></i>
                    </div>

                </div>
            </div>

        </div>


    </div>


</template>

<script>


    let input = null;

    let self;
    let canvas;


    export default {


        props: ['link', 'id', 'idx', 'jdx', 'type', 'height', 'required', 'preload', 'label', 'mode', 'forId', 'callback', 'images', 'limit', 'cropRatio'],
        components: {},
        data() {
            return {
                componentKey: 1,
                star: null,
                doc: this.preload,
                reader: null,

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
//            $(".point-sw")

            this.uploader = document.querySelector('#' + this.id + '-file');
            this.uploadContainer = document.querySelector('#container-' + this.id);
        }
        ,
        created() {

        }
        ,
        updated() {

        },
        beforeUpdate() {
        }
        ,
        methods: {
            async getBase64(file, is_url = false) {

                if (is_url) {
                    file = await (axios.get(file, {responseType: 'blob'}));
                    file = file.data
                }
                let reader = new FileReader();
                return new Promise(resolve => {
                    reader.onload = ev => {
                        resolve(ev.target.result)
                    };
                    reader.onerror = ev => {
                        resolve(null)
                    };
                    reader.readAsDataURL(file)
                });

            },
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


            },


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

            async filePreview(event, id) {
                let file;


                if (event.dataTransfer) {
                    file = event.dataTransfer.files[0];
                }
                else if (event.target.files) {
                    file = event.target.files[0];
                }
                event.target.value = '';

                this.doc = await this.getBase64(file);

                this.$parent.content.body[this.idx][this.jdx].value = this.doc;
//                this.doc = URL.createObjectURL(file);
                this.creating = false;
                this.uploadContainer.classList.add('d-none');


            },

            log(s) {
                console.log(s);
            }


        }
    }


</script>

<style type="text/css" lang="scss">


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