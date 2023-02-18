<template>

    <div class=" ">


        <div :class=" panel || view=='horizontal'? '': 'card-container '" class="my-4">

            <!--admin panel-->
            <div v-if=" panel  " class="row mx-1 ">

                <div class="position-absolute end-0 top-0     "
                     style="z-index: 2"
                >
                              <span class=" rounded mx-1   " :title="data.active? 'غیر فعال سازی':'فعال سازی'"
                                    :class="data.active? 'bg-success hoverable-primary':'bg-dark   hoverable-success'"
                                    @click="toggleActivate({'id':data.id,'active': data.active})">
                                 <i class="fa   text-white   mx-2 "
                                    :class="data.active? 'fa-check-circle':'fa-minus-circle'"
                                    aria-hidden="true"></i>
                            </span>
                    <span class="bg-danger rounded    hoverable-primary  "

                          @click="  showDialog('confirm','از حذف اطمینان دارید؟', remove ,data.id)">
                                 <i class="fa   fa-trash  text-white   mx-2 "
                                    aria-hidden="true"></i>
                            </span>


                </div>
                <a :href="'/panel/video/edit/'+data.id" class="m-1 d-block  ">
                    <div class="card move-on-hover">
                        <div class="card-body  p-3 ">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <div class="  mb-1 text-primary font-weight-bold">
                                            {{ data.title }}
                                        </div>
                                        <div class="small text-secondary text-opacity-75 hoverable-text-dark"
                                             @click.prevent="params.category=data.category_id;getData(1)">
                                            {{ getCategory(data.category_id) }}
                                        </div>

                                        <div class="small text-blue text-opacity-75  ">

                                            {{ getDuration(data.duration) }}
                                        </div>
                                        <div class="small text-danger text-opacity-75  ">
                                            {{ getDateTime(data.created_at) }}
                                        </div>

                                    </div>
                                </div>
                                <div class="col-4 text-end  " style="height: 5rem !important;">

                                    <img :src="getImage(data.id )" class="rounded  w-100 h-100 "
                                         style="object-fit: contain"
                                         alt="" @error="imgError">


                                </div>
                            </div>
                        </div>
                    </div>
                </a>

            </div>
            <!--public  search-->

            <div v-if=" !panel && view=='vertical'" class="row mx-1 card-width   mb-1">
                <div class="    ">
                    <a :href="getLink(data.id )"
                       class="m-card  d-flex align-items-start align-content-around flex-column    "
                       data-toggle="modal"
                       :key="data.id">
                        <div class="  bg-transparent  w-100 ">

                            <div class="d-flex justify-content-between position-relative    mx-1 opacity-0"
                                 style="z-index: 3;">

                                <div class="position-absolute    text-white m-2  small-1"
                                     data-toggle="tooltip"
                                     data-placement="top"
                                     title="دسته بندی">
                                    <i v-if="data.category_id" class="fa fa-male fa-2x" aria-hidden="true"></i>
                                    <i v-else="" class="fa fa-female fa-2x" aria-hidden="true"></i>
                                </div>

                            </div>

                            <div class=" position-relative     m-2">
                                <a :href="getLink(data.id )"
                                   class="d-block overflow-hidden rounded-5  shadow-2-soft">
                                    <div class="  position-absolute rounded-5   img-overlay">▶️</div>
                                    <img class="  w-100 rounded-5   "
                                         style="z-index: 0;height: 11rem"
                                         @error="imgError"
                                         :src="getImage(data.id )" alt="">
                                    <div class="row position-absolute bottom-0 start-0 end-0 opacity-80">
                                        <div class="col">
                                            <span @click.prevent="params.category=data.category_id;getData(1)"
                                                  class="  rounded-start hoverable-dark bg-primary text-white small   px-2 ">  {{
                                                    getCategory(data.category_id)
                                                }}
                                            </span>
                                            <span
                                                class="   rounded-end  bg-secondary text-white small   px-2 ">   {{
                                                    getDuration(data.duration)
                                                }}
                                                </span>
                                        </div>

                                    </div>
                                </a>
                            </div>

                        </div>

                        <!--<img v-else src="img/school-no.png" alt=""></div>-->
                        <div
                            class="m-card-body  px-2 d-flex  flex-column  align-self-stretch    text-center z-index-1">

                            <div class="text-indigo   text-end max-2-line " style="font-size:13px"> {{
                                    getDateTime(data.created_at)
                                }}
                            </div>
                            <div class="card-divider opacity-50"></div>
                            <div class="text-primary small  my-1 text-start max-2-line "> {{ data.title }}</div>


                        </div>
                        <div class="m-card-footer  bg-transparent position-relative w-100 py-1">
                            <!--                                <img class="   back-footer-img position-absolute w-100 top-0"-->
                            <!--                                     :src="assetLink+'/card-footer.png'"-->
                            <!--                                     alt="" style=" ">-->

                        </div>

                    </a>
                </div>
            </div>


        </div>


    </div>

</template>

<script>

let self;
let scrolled = false;
// import paginator from '../components/pagination.vue';
// import slider from '../components/slider.vue';
//    import {computed} from 'vue'
// import {Swiper, SwiperSlide,} from 'swiper/vue';

// Import Swiper styles
// import 'swiper/css';
//    import 'swiper/css/navigation';
//    import 'swiper/css/pagination';
//    import 'swiper/css/scrollbar';
//    import "swiper/css/free-mode"

export default {
    props: ['admin', 'panel', 'data', 'idx', 'editLink', 'removeLink', 'user-data', 'categoryData', 'imgLink', 'assetLink', 'urlParams', 'type',],

    // components: {paginator, slider, Swiper, SwiperSlide,},
    data() {
        return {
//                modules: [/*Navigation,*/   Scrollbar],
            url: window.location.href.split('?')[0],
            collapsed: false,
            params: {order_by: 'created_at', dir: 'DESC'},
            categories: JSON.parse(this.categoryData),
            users: this.userData ? JSON.parse(this.userData) : [],
            filter: false,
            pinSearch: false,
            view: 'vertical',
            loader: null,
        }
    },
    provide() {
        return {
//                paginator: computed(() => this.pagination)
        }
    },
    created() {

        if (this.urlParams) {
            this.params = JSON.parse(this.urlParams);
        }

    },
    watch: {
//            scroll(value, oldValue) {
//                console.log(value);
//                console.log(oldValue);
//
//            }
    },

    setup() {

    },
    mounted() {
        self = this;
        this.loader = document.querySelector('.progress-line');
    }, methods: {

        toggleActivate(d) {
            this.showDialog('confirm', 'از ' + (d.active ? 'غیر فعال سازی' : 'فعال سازی') + ' اطمینان دارید؟', this.edit, {
                'id': d.id,
                'active': !d.active
            });

        },
        getDuration(t) {
            if (t == null) return '';
            return `${Math.floor(t / 60)}:${t % 60}`;
        },
        getDateTime(date) {
            if (date == null) return '';
            let d = new Date(date);
            const options = {/*weekday: 'long', year: 'numeric',*/
                month: 'long',
                day: 'numeric',
                // hour: 'numeric',
                // minute: 'numeric'
            };

            let seconds = Math.floor((new Date() - d) / 1000);

            let interval = seconds / 31536000;

            if (interval > 1) {
                return (d.toLocaleDateString('fa-IR', {year: 'numeric', month: 'long', day: 'numeric',}));
                // return Math.floor(interval) + " سال قبل";
            }
            interval = seconds / 2592000;
            if (interval > 1) {
                return (d.toLocaleDateString('fa-IR', {month: 'long', day: 'numeric',}));
                // return Math.floor(interval) + " ماه قبل";
            }
            interval = seconds / 86400;
            if (interval > 1) {
                return (d.toLocaleDateString('fa-IR', {month: 'long', day: 'numeric',}));
                // return Math.floor(interval) + " روز قبل";
            }
            interval = seconds / 3600;
            if (interval > 1) {
                return Math.floor(interval) + " ساعت قبل";
            }
            interval = seconds / 60;
            if (interval > 1) {
                return Math.floor(interval) + " دقیقه قبل";
            }
            if (seconds > 0)
                return Math.floor(seconds) + " ثانیه قبل";
            return `${d.toLocaleDateString('fa-IR', {
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric'
            })}`;


        },
        getCategory(id) {
            let s = this.categories.find(function (el) {
                return el.id == id;
            });
            if (s) return s.name; else return '';
        },

        getLink(id) {

//                switch (type) {
//                    case 'pl':
            return '/video/' + id;
//                        break;
//                    case 'co':
//                        return '/coach/' + id;
//                        break;
//                    case 'cl':
//                        return '/club/' + id;
//                        break;
//                    case 'sh':
//                        return '/shop/' + id;
//                        break;

//                }
        },

        getImage(id) {

            if (!id)
                return this.assetLink + "/noimage.png";
            return this.imgLink + "/" + this.type + "/" + id + ".jpg";

        },
        showDialog(type, message, click, params) {
            window.showDialog(type, message, onclick = () => click, params);


        },
        log(str) {
            console.log(str);
        },
        imgError(event) {

            event.target.src = '/img/noimage.png';
            event.target.parentElement.href = '/img/noimage.png';
        },
        remove(id) {
            this.loading = true;

            axios.post(this.removeLink, {id: id}, {})
                .then((response) => {
//                        console.log(response);
                        this.loading = false;
                        if (response.status === 200)
                            window.location.reload();
                    }
                ).catch((error) => {
                this.loading = false;
                let errors = '';
                if (error.response && error.response.status === 422)
                    for (let idx in error.response.data.errors)
                        errors += error.response.data.errors[idx] + '<br>';
                else {
                    errors = error;
                }
                window.showToast('danger', errors);
            });
        },
        edit(data) {
            this.loading = true;
            axios.post(this.editLink, data, {})

                .then((response) => {
//                        console.log(response);
                        this.loading = false;

                        if (response.status === 200) {

                            if (response.data && response.data.status === 'success' && response.data.msg)
                                window.showToast('success', response.data.msg);
                            if (response.data.msg.includes('فعال شد'))
                                window.location.reload();

                        }
//                            window.location.reload();


                    }
                ).catch((error) => {
                this.loading = false;

                let errors = '';
                if (error.response && error.response.status === 422)
                    for (let idx in error.response.data.errors)
                        errors += error.response.data.errors[idx] + '<br>';
                else {
                    errors = error;
                }
                window.showToast('danger', errors);

            });
        },


    }
}
</script>
