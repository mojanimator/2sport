<template>

    <div class="position-relative container-fluid w-100   card shadow-lg   blur ">

        <Swiper class="position-absolute start-0 end-0 p-2 rounded bg-light  "
                :centeredSlides="false"
                :slides-per-view="'auto'"
                :grabCursor="true"
                :space-between="10"
                :freeMode="true"
                :slidesPerColumnFill="'row'"

        >
            <swiper-slide title="فیلتر" class="w-auto my-auto text-secondary">
                <i class="fa fa-filter" aria-hidden="true"></i>
            </swiper-slide>

            <swiper-slide style="width: 12rem">
                <div class="    input-group   my-2">

                    <input id="name" type="text"
                           class="  px-4 form-control   "
                           v-model="params.name"
                           @keyup.enter="getData(1)" placeholder="عنوان/تگ"
                           autofocus>


                </div>
            </swiper-slide>
            <swiper-slide style="width: 12rem">
                <select @change="getData(1)" class="px-4 my-2 form-control   "

                        v-model="params.category"
                >
                    <option style="border-top-right-radius: 0;border-top-left-radius: 0" value="">دسته بندی</option>
                    <option class="text-dark" v-for="category in categories"
                            :value="category.id">
                        {{ category.name }}

                    </option>
                </select>
            </swiper-slide>

            <swiper-slide v-show="admin && users.length>0" style="width: 12rem">
                <select class="px-4 my-2  form-control "

                        v-model="params.user"
                        @change=" getData(1)">
                    <option value=""> کاربر</option>
                    <option class="text-dark" v-for="user in users"
                            :value="user.id">
                        {{ user.name }}

                    </option>
                </select>
            </swiper-slide>


            <swiper-slide v-show="admin" style="width: 10rem">

                <div class="btn-group w-100  my-2">
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


            <!--<div class="row mx-auto my-2">-->

            <!--<div class="col-sm-2 btn-group px-1 bg-transparent  my-1">-->
            <!--<button class="btn     px-2   " type="button"-->
            <!--:class="filter?'btn-primary' :'btn-outline-primary'"-->
            <!--title="فیلتر" data-toggle="tooltip" data-placement="bottom"-->
            <!--@click="filter=!filter">-->

            <!--<i class="fa fa-search" aria-hidden="true"></i>-->


            <!--</button>-->

            <!--<button class="btn     px-2   btn-outline-primary  " type="button"-->
            <!--@click="view=='horizontal'? view='vertical' :view='horizontal'"-->
            <!--title="نمایش" data-toggle="tooltip" data-placement="bottom">-->
            <!--<span v-if="view=='horizontal'">-->
            <!--<i class="fa fa-list-ul" aria-hidden="true"></i>-->
            <!--</span>-->
            <!--<span v-if="view=='vertical'">-->
            <!--<i class="fa fa-th" aria-hidden="true"></i>-->
            <!--</span>-->

            <!--</button>-->
            <!--</div>-->
            <!--</div>-->

        </Swiper>

        <div class="        shadow-card  " style="margin-top: 4.5rem">

            <div
                class=" col-12 mx-auto  mb-3 row     font-weight-bold text-primary border border-2 border-primary rounded bg-light p-2 mb-2 z-index-0 text-center ">
                <div class="col-4 px-0 hover-underline small " :class="params.order_by=='created_at'?'active':''"
                     @click="params.order_by='created_at';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC';  ;getData(1);">
                    تاریخ ثبت
                </div>
                <div class="col-4 px-0 hover-underline small "
                     :class="params.order_by=='duration'  ?'active':''"
                     @click="params.order_by='duration';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC' ;getData(1);">
                    مدت
                </div>
                <div class="col-4 px-0 hover-underline small"
                     :class="params.order_by=='category_id'  ?'active':''"
                     @click="params.order_by='category_id';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC';getData(1);">
                    دسته بندی
                </div>

            </div>

            <div v-show="loading" class="  position-absolute start-0 end-0 text-center" style="margin-top: -1rem">
                <div v-for="i in  [1,1,1,1,1]"
                     class="spinner-grow mx-1   spinner-grow-sm text-primary "
                     role="status">
                    <span class="visually-hidden"> </span>
                </div>

            </div>

            <div :class=" panel || view=='horizontal'? '': 'card-container '" class="my-4">


                <!--admin panel-->
                <div v-if=" panel  " class="row mx-1 ">
                    <div v-for="d,idx in data" class="col-md-6 col-xl-4 px-0 px-sm-1  position-relative ">
                        <div class="position-absolute end-0 top-0     "
                             style="z-index: 2"
                        >
                              <span class=" rounded mx-1   " :title="d.active? 'غیر فعال سازی':'فعال سازی'"
                                    :class="d.active? 'bg-success hoverable-primary':'bg-dark   hoverable-success'"
                                    @click="toggleActivate({'id':d.id,'active': d.active})">
                                 <i class="fa   text-white   mx-2 "
                                    :class="d.active? 'fa-check-circle':'fa-minus-circle'"
                                    aria-hidden="true"></i>
                            </span>
                            <span class="bg-danger rounded    hoverable-primary  "

                                  @click="  showDialog('confirm','از حذف اطمینان دارید؟', remove ,d.id)">
                                 <i class="fa   fa-trash  text-white   mx-2 "
                                    aria-hidden="true"></i>
                            </span>


                        </div>
                        <a :href="'/panel/video/edit/'+d.id" class="m-1 d-block  ">
                            <div class="card move-on-hover">
                                <div class="card-body  p-3 ">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <div class="  mb-1 text-primary font-weight-bold">
                                                    {{ d.title }}
                                                </div>
                                                <div class="small text-secondary text-opacity-75 hoverable-text-dark"
                                                     @click.prevent="params.category=d.category_id;getData(1)">
                                                    {{ getCategory(d.category_id) }}
                                                </div>

                                                <div class="small text-blue text-opacity-75  ">

                                                    {{ getDuration(d.duration) }}
                                                </div>
                                                <div class="small text-danger text-opacity-75  ">
                                                    {{ getDateTime(d.created_at) }}
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-4 text-end  " style="height: 5rem !important;">

                                            <img :src="getImage(d.id )" class="rounded  w-100 h-100 "
                                                 style="object-fit: contain"
                                                 alt="" @error="imgError">


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

                        <a :href="'/video/'+d.id" class="m-1  d-block">
                            <div class="card move-on-hover">
                                <div class="card-body py-2 px-1 ">
                                    <div class="row">
                                        <div class="col-4    align-self-center   rounded pe-auto ps-1 "
                                             style="height: 7rem !important;">

                                            <img :src="getImage(d.docs )" class="rounded-2  w-100   h-100"
                                                 style="object-fit: contain"


                                                 alt="" @error="imgError">


                                        </div>
                                        <div class="col-8">
                                            <div class="numbers">
                                                <div class="  mb-1 text-primary font-weight-bold">
                                                    نام: {{ d.title }}
                                                </div>
                                                <div class="small text-secondary text-opacity-100    ">
                                                    دسته بندی: {{ getCategory(d.category_id) }}

                                                </div>

                                                <div class="small text-indigo  text-opacity-100     ">
                                                    <span class="mx-1"> زمان: {{ d.created_at }}</span>

                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div v-if=" !panel && view=='vertical'" class="row mx-1">
                    <div v-for="d,idx in data" class=" card-width   mb-1   ">
                        <a :href="getLink(d.id, type)"
                           class="m-card  d-flex align-items-start align-content-around flex-column    "
                           data-toggle="modal"
                           :key="d.id">
                            <div class="  bg-transparent  w-100 ">

                                <div class="d-flex justify-content-between position-relative    mx-1 opacity-0"
                                     style="z-index: 3;">

                                    <div class="position-absolute    text-white m-2  small-1"
                                         data-toggle="tooltip"
                                         data-placement="top"
                                         title="دسته بندی">
                                        <i v-if="d.category_id" class="fa fa-male fa-2x" aria-hidden="true"></i>
                                        <i v-else="" class="fa fa-female fa-2x" aria-hidden="true"></i>
                                    </div>

                                </div>

                                <div class=" position-relative     m-2">
                                    <a :href="getLink(d.id, type)"
                                       class="d-block overflow-hidden rounded-5  shadow-2-soft">
                                        <div class="  position-absolute rounded-5   img-overlay">▶️</div>
                                        <img class="  w-100 rounded-5   "
                                             style="z-index: 0;height: 11rem"
                                             @error="imgError"
                                             :src="getImage(d.id )" alt="">
                                        <div class="row position-absolute bottom-0 start-0 end-0 opacity-80">
                                            <div class="col">
                                            <span @click.prevent="params.category=d.category_id;getData(1)"
                                                  class="  rounded-start hoverable-dark bg-primary text-white small   px-2 ">  {{
                                                    getCategory(d.category_id)
                                                }}
                                            </span>
                                                <span
                                                    class="   rounded-end  bg-secondary text-white small   px-2 ">   {{
                                                        getDuration(d.duration)
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
                                        getDateTime(d.created_at)
                                    }}
                                </div>
                                <div class="card-divider opacity-50"></div>
                                <div class="text-primary small  my-1 text-start max-2-line "> {{ d.title }}</div>


                            </div>
                            <div class="m-card-footer  bg-transparent position-relative w-100 py-1">
                                <!--                                <img class="   back-footer-img position-absolute w-100 top-0"-->
                                <!--                                     :src="assetLink+'/card-footer.png'"-->
                                <!--                                     alt="" style=" ">-->

                            </div>

                        </a>
                    </div>
                </div>
                <h4 v-if=" noData" class="text-center mt-3 text-primary ">
                    نتیجه ای یافت نشد

                </h4>


            </div>


        </div>
        <!--<paginator class="   my-1  " ref="paginator"></paginator>-->
        <div class="progress-line mt-1" :style="loading?'opacity:1;':'opacity:0;'"></div>

        <!--<button class="btn btn-secondary rounded col-sm-4 my-1" type="button"-->
        <!--id="name-addon"-->
        <!--@click="getData(1)">-->
        <!--جست و جو-->
        <!--</button>-->
    </div>

</template>

<script>

let self;
let scrolled = false;
import paginator from '../components/pagination.vue';
import slider from '../components/slider.vue';
//    import {computed} from 'vue'
import {Swiper, SwiperSlide,} from 'swiper/vue';

// Import Swiper styles
import 'swiper/css';
//    import 'swiper/css/navigation';
//    import 'swiper/css/pagination';
//    import 'swiper/css/scrollbar';
//    import "swiper/css/free-mode"

export default {
    props: ['price', 'admin', 'panel', 'dataLink', 'editLink', 'removeLink', 'user-data', 'category-data', 'imgLink', 'assetLink', 'urlParams', 'type',],

    components: {paginator, slider, Swiper, SwiperSlide,},
    data() {
        return {
//                modules: [/*Navigation,*/   Scrollbar],
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
        this.setScroll(this.loader);
        this.getData(1);

        if (!this.params.category) this.params.category = "";
        if (!this.params.user) this.params.user = "";
    }, methods: {

        toggleActivate(d) {
            this.showDialog('confirm', 'از ' + (d.active ? 'غیر فعال سازی' : 'فعال سازی') + ' اطمینان دارید؟', this.edit, {
                'id': d.id,
                'active': !d.active
            });

        },
        getDuration(t) {
            if (t == null) return '';
            let min = Math.floor(t / 60);
            let sec = t % 60;
            return `${min < 10 ? '0' + min : min}:${sec < 10 ? '0' + sec : sec}`;
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

        getLink(id, type) {

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
            return this.imgLink + "/" + '9' + "/" + id + ".jpg";

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
                category: this.params.category ? this.params.category : "",
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

//                            console.log(axios.getUri({url: this.url, params: response.config.params}));
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
                console.log(error);
                this.loading = false;
            });
        },

    }
}
</script>
