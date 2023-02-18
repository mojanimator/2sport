<template>

    <div class=" container-fluid   card shadow-lg   blur ">
        <Swiper class="position-absolute start-0 end-0 p-2 rounded bg-light "
                :centeredSlides="false"
                :slides-per-view="'auto'"
                :grabCursor="true"
                :space-between="10"
                :freeMode="true"


        >
            <swiper-slide title="فیلتر" class="w-auto my-auto text-secondary"><i class="fa fa-filter"
                                                                                 aria-hidden="true"></i>
            </swiper-slide>
            <swiper-slide style="width: 16rem">
                <div class=" my-2   input-group  ">

                    <input id="name" type="text"
                           class="  px-4 form-control   "
                           v-model="params.name"
                           @keyup.enter="getData(1)" placeholder="نام، نام کاربری، تلفن، ایمیل"
                           autofocus>
                    <!--<label for="search"-->
                    <!--class="col-md-12    form-label  text-md-right">نام بازیکن </label>-->


                </div>
            </swiper-slide>


            <swiper-slide v-if="admin" style="width: 10rem">
                <div class="btn-group w-100 my-2">
                    <input
                        type="radio"
                        class="btn-check"
                        name="active"
                        id="active-all"
                        autocomplete="off"
                        value=""
                        v-model="params.active"
                        @change="getData(1)"
                        checked
                    />
                    <label class="btn btn-outline-primary px-1" for="active-all">وضعیت</label>

                    <input @change="getData(1)" v-model="params.active" :value="1" type="radio" class="btn-check"
                           name="active"
                           id="active-active"
                           autocomplete="off"/>
                    <label class="btn btn-outline-primary px-1" for="active-active">فعال</label>

                    <input @change="getData(1)" v-model="params.active" :value="0" type="radio" class="btn-check"
                           name="active"
                           id="active-deactive"
                           autocomplete="off"/>
                    <label class="btn btn-outline-primary px-1" for="active-deactive">غیرفعال</label>
                </div>
            </swiper-slide>


        </Swiper>


        <div class="        shadow-card " style="margin-top: 4.5rem">

            <div
                class=" col-12 mx-auto  mb-3 row     font-weight-bold text-primary border border-2 border-primary rounded bg-light p-2 mb-2 z-index-0 text-center ">
                <!--<div class="col-6 hover-underline "-->
                <!--:class="params.order_by=='created_at' &&  params.dir =='DESC' ?'active':''"-->
                <!--@click="params.order_by='created_at';   params.dir ='DESC';   getData(1);">-->
                <!--جدیدترین-->
                <!--</div>-->
                <div class="col-6 px-0 hover-underline small " :class="params.order_by=='created_at'?'active':''"
                     @click="params.order_by='created_at';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC';  ;getData(1);">
                    تاریخ ثبت
                </div>
                <div class="col-6 px-0 hover-underline small "
                     :class="params.order_by=='active'  ?'active':''"
                     @click="params.order_by='active';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC' ;getData(1);">
                    وضعیت
                </div>

            </div>
            <div v-show="loading" class="  position-absolute start-0 end-0   text-center" style="margin-top: -1rem">
                <div v-for="i in  [1,1,1,1,1]"
                     class="spinner-grow mx-1   spinner-grow-sm text-primary "
                     role="status">
                    <span class="visually-hidden"> </span>
                </div>

            </div>

            <div :class=" panel || view=='horizontal'? '': 'card-container'" class="my-4">

                <!--admin panel-->
                <div v-if=" panel" class="row mx-1 ">
                    <div v-for="d,idx in data" class="col-md-6 col-xl-4 px-0 px-sm-1  position-relative ">
                        <div class="position-absolute end-0 top-0     "
                             style="z-index: 2"
                        >
                                <span class=" rounded mx-1    " :title="d.active? 'غیر فعال سازی':'فعال سازی'"
                                      :class="d.active? 'bg-success hoverable-primary':'bg-dark   hoverable-success'"
                                      @click="toggleActivate({'id':d.id,'active': d.active})">

                                    <i class="fa fa-check-circle  text-white   mx-2 "

                                       :class="d.active? 'fa-check-circle':'fa-minus-circle'"
                                       aria-hidden="true"></i>
                            </span>
                            <!--<span class="bg-danger rounded   p-1 hoverable-primary  "-->

                            <!--@click="  showDialog('confirm','از حذف اطمینان دارید؟', remove ,d.id)">-->
                            <!--<i class="fa   fa-trash  text-white   mx-2 "-->
                            <!--aria-hidden="true"></i>-->
                            <!--</span>-->


                        </div>
                        <a :href="'/panel/user/edit/'+d.id" class="m-1 d-block  ">
                            <div class="card move-on-hover">
                                <div class="card-body  p-3 ">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="small">
                                                <div class="small  text-primary font-weight-bold">
                                                    {{ 'نام: ' + (d.name ? d.name : '') + ' ' + (d.family ? d.family : '') }}
                                                </div>
                                                <div class="small   text-opacity-75    my-1">
                                                    {{ 'نام کاربری: ' + (d.username ? d.username : '') }}
                                                </div>
                                                <div class="small text-secondary text-opacity-75   my-1">
                                                    {{ 'تلفن: ' + d.phone }}

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end  " style="height: 5rem !important;">

                                            <i class="fa fa-user text-primary fa-2x" aria-hidden="true"></i>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!--public  search-->
                <div v-if=" !panel && view=='horizontal'" class="row mx-1 ">
                    <div v-for="d,idx in data" class="col-md-6 col-xl-4 px-0 px-sm-1  position-relative ">

                        <a :href="'/coach/'+d.id" class="m-1 d-block  ">
                            <div class="card move-on-hover">
                                <div class="card-body  py-2 px-1 ">
                                    <div class="row">
                                        <div class="col-4    align-self-center  rounded pe-auto ps-1 "
                                             style="height: 7rem !important;">

                                            <img :src="getImage(d.docs )" class="rounded-2 w-100    h-100"
                                                 style="object-fit: contain"


                                                 alt="" @error="imgError">


                                        </div>
                                        <div class="col-8">
                                            <div class="numbers ms-1">
                                                <div class="  mb-1 text-primary font-weight-bold">
                                                    {{ d.name + ' ' + d.family }}
                                                </div>
                                                <div class="small text-secondary text-opacity-100    ">
                                                    {{ getSport(d.sport_id) }}

                                                </div>
                                                <div class="small  text-opacity-100     ">
                                                    {{ }}

                                                </div>
                                                <div class="small text-indigo  text-opacity-100     ">
                                                    <span class="mx-1"> سن: {{ getAge(d.born_at) }}</span>

                                                </div>
                                                <div class="small text-success text-opacity-100    ">
                                                    همراه: {{ d.phone }}

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <h4 v-if=" noData" class="text-center mt-3 text-primary">
                    نتیجه ای یافت نشد

                </h4>


            </div>


        </div>
        <!--<paginator class="   my-1  " ref="paginator"></paginator>-->
        <div class="progress-line mt-1" :style="loading?'opacity:1;':'opacity:0;'"></div>

    </div>

</template>

<script>

let scrolled = false;
let self;
import paginator from '../components/pagination.vue';
import slider from '../components/slider.vue';
//    import {computed} from 'vue'

import {Swiper, SwiperSlide,} from 'swiper/vue';

// Import Swiper styles
import 'swiper/css';

export default {
    props: ['price', 'admin', 'panel', 'dataLink', 'editLink', 'removeLink', 'user-data', 'province-data', 'county-data', 'sport-data', 'imgLink', 'assetLink', 'urlParams', 'type',],

    components: {paginator, slider, Swiper, SwiperSlide,},
    data() {
        return {
            url: window.location.href.split('?')[0],
            collapsed: false,
            data: [],
            scroll: true,
            paginate: 12,
            loading: false,
            total: -1,

            groups: [],
            groups_show_tree: true,
            noData: false,
            page: 1,
            pagination: {},
            params: {order_by: 'created_at', dir: 'DESC'},


            filter: false,
            pinSearch: false,
            view: 'horizontal', loader: null,
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
        this.setScroll(this.loader);
        this.getData(1);

    }, methods: {

        setScroll(el) {
            window.onscroll = function () {
//                    const {top, bottom, height} = this.loader.getBoundingClientRect();

                let top_of_element = el.offsetTop;
                let bottom_of_element = el.offsetTop + el.offsetHeight;
                let bottom_of_screen = window.pageYOffset + window.innerHeight;
                let top_of_screen = window.pageYOffset;


                if ((bottom_of_screen + 300 > top_of_element) && (top_of_screen < bottom_of_element + 200) && !self.loading) {
                    self.getData();
                    scrolled = true;
//                        console.log('visible')
                    // the element is visible, do something
                } else {
//                        console.log('invisible')
                    // the element is not visible, do something else
                }
            };
        },
        toggleActivate(d) {
            this.showDialog('confirm', 'از ' + (d.active ? 'غیر فعال سازی' : 'فعال سازی') + ' اطمینان دارید؟', this.edit, {
                'id': d.id,
                'active': !d.active
            });

        },
        getAge(timestamp) {
            let today = new Date();
            let birthDate = new Date(timestamp * 1000);
            let age = today.getFullYear() - birthDate.getFullYear();
            let m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            return age;
        },


        showDialog(type, message, click, params) {
            window.showDialog(type, message, onclick = () => click, params);


        },
        getLink(id, type) {

//                switch (type) {
//                    case 'pl':
            return '/user/' + id;
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

        log(str) {
            console.log(str);
        },
        imgError(event) {

            event.target.src = '/img/noimage.png';
            event.target.parentElement.href = '/img/noimage.png';
        },
        setEvents(el) {

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
        getData(page) {
            if (page) {
                this.params.page = page;
                if (page === 1) {
                    this.data.splice(0, this.data.length);
                }
            }
            if (this.params.total > 0 && this.params.total <= this.data.length)
                return false; //no data, dont try


//                this.loading.removeClass('hide');
            this.loading = true;
            this.noData = false;

            this.params = {
                name: this.params.name ? this.params.name : this.name,
                page: !isNaN(this.params.page) ? this.params.page : this.page,
                paginate: this.params.paginate ? this.params.paginate : this.paginate,
                order_by: this.params.order_by ? this.params.order_by : 'created_at',
                dir: this.params.dir ? this.params.dir : 'DESC',
                gender: this.params.gender ? this.params.gender : null,
                sport: this.params.sport ? this.params.sport : "",
                province: this.params.province ? this.params.province : "",
                county: this.params.county ? this.params.county : "",
                age_h: this.$refs.ageSlider ? this.$refs.ageSlider.maxVal : null,
                age_l: this.$refs.ageSlider ? this.$refs.ageSlider.minVal : null,

                active: this.params.active !== null ? this.params.active : "",
                user: this.params.user ? this.params.user : "",
                panel: this.panel,
            };

            history.replaceState(null, null, axios.getUri({url: this.url, params: this.params}));
//                this.log(this.params);
//                this.log(axios.getUri({url: this.url, params: this.params}));

            axios.get(this.dataLink, {
                params: this.params
            })
                .then((response) => {

//                           console.log(axios.getUri({url: this.url, params: response.config.params}));
//                        this.loading.addClass('hide');
                        if (response.status === 200) {


//                                console.log(response.data.data);
                            Array.prototype.push.apply(this.data, response.data.data);


//                                this.page = response.data.current_page + 1;

                            this.loading = false;
                            if (this.data.length === 0)
                                this.noData = true;
                            if (this.params.page > 1 && this.data.length === 0) {
                                this.noData = false;
                                this.params.page = 1;
                                this.getData(1);
                                return;
                            }
                            this.params.total = response.data.total;
                            this.params.page = response.data.current_page + 1;
//                                this.pagination =
//                                    {
//                                        current_page: response.data.current_page,
//                                        first_page_url: response.data.first_page_url,
//                                        next_page_url: response.data.next_page_url,
//                                        prev_page_url: response.data.prev_page_url,
//                                        last_page_url: response.data.last_page_url,
//                                        last_page: response.data.last_page,
//                                        from: response.data.from,
//                                        to: response.data.to,
//                                        total: response.data.total,
//                                    };
//
//
//                                this.$refs.paginator.updatePaginator(this.pagination);

//                                this.$forceUpdate();
                        }
                    }
                ).catch((error) => {
//                    console.log(error);
                this.loading = false;
            });
        },

    }
}
</script>
