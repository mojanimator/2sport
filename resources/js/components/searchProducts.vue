<template>

    <div class=" container-fluid    card shadow-lg   blur ">

        <Swiper class="position-absolute start-0 end-0 p-2 rounded bg-light"
                :centeredSlides="false"
                :slides-per-view="'auto'"
                :grabCursor="true"
                :space-between="10"
                :freeMode="true"


        >
            <swiper-slide title="فیلتر" class="w-auto my-auto text-secondary"><i class="fa fa-filter"
                                                                                 aria-hidden="true"></i>
            </swiper-slide>
            <swiper-slide style="width: 8rem">
                <div class=" my-2   input-group  h-100">

                    <input id="name" type="text"
                           class="  px-4 form-control   "
                           v-model="params.name"
                           @keyup.enter="getData(1)" placeholder="نام محصول"
                           autofocus>
                    <!--<label for="search"-->
                    <!--class="col-md-12    form-label  text-md-right">نام بازیکن </label>-->


                </div>
            </swiper-slide>
            <swiper-slide style="width: 8rem">
                <select @change="getData(1)" class="px-4 my-2 form-control "

                        v-model="params.shop"
                >
                    <option value="">فروشگاه</option>
                    <option class="text-dark" v-for="shop in shops"
                            :value="shop.id">
                        {{ shop.name }}

                    </option>
                </select>
            </swiper-slide>


            <swiper-slide style="width: 15rem">
                <div class="   my-2 mx-1    ">
                    <slider class="   " id="priceSlider" ref="priceSlider" :min="0"
                            :max="10000000" :step="10000"
                            label="محدوده قیمت (تومان)" :getData="getData">

                    </slider>

                </div>
            </swiper-slide>
            <swiper-slide v-if="admin && users.length>0" style="width: 8rem">
                <select class="px-4 my-2  form-control "

                        v-model="params.user"
                        @change=" getData(1)">
                    <option value="">همه کاربران</option>
                    <option class="text-dark" v-for="user in users"
                            :value="user.id">
                        {{ user.name }}

                    </option>
                </select>
            </swiper-slide>
            <swiper-slide v-if="   admin" style="width: 10rem">
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


        </Swiper>


        <div class="   " style="margin-top: 4.5rem">

            <div class="   mx-auto  mb-3 row     font-weight-bold text-primary border border-2 border-primary rounded bg-light p-2 mb-2 z-index-0 text-center ">
                <div class="col-6 px-0 hover-underline small" :class="params.order_by=='created_at'?'active':''"
                     @click="params.order_by='created_at';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC';  ;getData(1);">
                    تاریخ ثبت
                </div>
                <div class="col-6 px-0 hover-underline small"
                     :class="params.order_by=='discount_price'  ?'active':''"
                     @click="params.order_by='discount_price';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC' ;getData(1);">
                    قیمت
                </div>

            </div>
            <div v-show="loading" class="  position-absolute  start-0 end-0 text-center" style="margin-top: -1rem">
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
                              <span class=" rounded mx-1   p-1 " :title="d.active? 'غیر فعال سازی':'فعال سازی'"
                                    :class="d.active? 'bg-success hoverable-primary':'bg-dark   hoverable-success'"
                                    @click="  showDialog('confirm','از '+(d.active?'غیر فعال سازی':'فعال سازی')+' اطمینان دارید؟' , edit ,{'id':d.id,'active':!d.active})">
                                 <i class="fa fa-check-circle  text-white   mx-2 "
                                    :class="d.active? 'fa-check-circle':'fa-minus-circle'"
                                    aria-hidden="true"></i>
                            </span>
                            <span class="bg-danger rounded   p-1 hoverable-primary  "

                                  @click="  showDialog('confirm','از حذف اطمینان دارید؟', remove ,d.id)">
                                 <i class="fa   fa-trash  text-white   mx-2 "
                                    aria-hidden="true"></i>
                            </span>


                        </div>
                        <a :href="'/panel/product/edit/'+d.id" class="m-1 d-block  ">
                            <div class="card move-on-hover">
                                <div class="card-body   py-2 px-1 ">
                                    <div class="row">
                                        <div class="col-4    align-self-center   rounded pe-auto ps-1 "
                                             style="height: 7rem !important;">

                                            <img :src="getImage(d.docs )" class="rounded-2  w-100   h-100"
                                                 style="object-fit: contain"


                                                 alt="" @error="imgError">


                                        </div>
                                        <div class="col-8">
                                            <div class="numbers">
                                                <h5 class="  mb-0 text-primary font-weight-bold">
                                                    {{d.name }}
                                                </h5>
                                                <div class="small text-secondary text-opacity-75 font-weight-bolder  my-1">
                                                    {{ getShop(d.shop_id)}}

                                                </div>
                                                <div class="small  text-opacity-75 font-weight-bolder  my-1">

                                                <span :class="d.discount_price != d.price? ' text-decoration-line-through ':''"
                                                      class="   text-danger text-opacity-75 small    px-2 ">
                                                    {{getPrice(d.price)}}
                                                </span>

                                                    <span v-if="d.discount_price != d.price"
                                                          class="   text-blue text-opacity-75 small   px-2 ">
                                                        {{getPrice(d.discount_price)}}
                                                    </span>


                                                </div>


                                            </div>
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

                        <a :href="'/product/'+d.id" class="m-1 d-block  ">
                            <div class="card move-on-hover">
                                <div class="card-body   py-2 px-1 ">
                                    <div class="row">
                                        <div class="col-4    align-self-center   rounded pe-auto ps-1 "
                                             style="height: 7rem !important;">

                                            <img :src="getImage(d.docs )" class="rounded-2  w-100   h-100"
                                                 style="object-fit: contain"


                                                 alt="" @error="imgError">


                                        </div>
                                        <div class="col-8">
                                            <div class="numbers">
                                                <h5 class="  mb-0 text-primary font-weight-bold">
                                                    {{d.name }}
                                                </h5>
                                                <div class="small text-secondary text-opacity-75 font-weight-bolder  my-1">
                                                    {{ getShop(d.shop_id)}}

                                                </div>
                                                <div class="small  text-opacity-75 font-weight-bolder  my-1">

                                                <span :class="d.discount_price != d.price? ' text-decoration-line-through ':''"
                                                      class="   text-danger text-opacity-75 small    px-2 ">
                                                    {{getPrice(d.price)}}
                                                </span>

                                                    <span v-if="d.discount_price != d.price"
                                                          class="   text-blue text-opacity-75 small   px-2 ">
                                                        {{getPrice(d.discount_price)}}
                                                    </span>


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
                            <div class="m-card-heade bg-transparent  w-100 ">

                                <div class="d-flex justify-content-between position-relative    mx-1"
                                     style="z-index: 3;">

                                    <!--<div class="position-absolute    text-white m-2  small-1"-->
                                    <!--data-toggle="tooltip"-->
                                    <!--data-placement="top"-->
                                    <!--title="جنسیت">-->
                                    <!--<i v-if="d.is_man" class="fa fa-male fa-2x" aria-hidden="true"></i>-->
                                    <!--<i v-else="" class="fa fa-female fa-2x" aria-hidden="true"></i>-->
                                    <!--</div>-->

                                </div>
                                <img class="back-header-im position-absolute w-100 " style="top: -3.5rem;z-index: 2"
                                     :src="assetLink+'/card-header.png'"
                                     alt="">
                                <div class=" position-relative w-100">
                                    <a :href="getLink(d.id, type)" class="d-block  ">
                                        <div class="  position-absolute  img-overlay">⌕</div>
                                        <img class="card-im  w-100 mt-4" style="z-index: 0;height: 12rem"
                                             @error="imgError"
                                             :src="getImage(d.docs )" alt="">
                                    </a>
                                </div>

                            </div>

                            <!--<img v-else src="img/school-no.png" alt=""></div>-->
                            <div class="m-card-body  px-2 d-flex  flex-column  align-self-stretch  justify-content-between  text-end z-index-1">

                                <div class=" row py-1 text-white bg-primary  my-1 text-center max-2-line "> {{d.name }}
                                </div>

                                <div class=" text-center     pt-1 ">
                                    <div class="row">
                                        <div class="col">
                                            <span class="  rounded-start   bg-primary text-white small   px-2 ">{{'کد'}}</span>
                                            <span class="   rounded-end  bg-secondary text-white small   px-2 ">  {{d.id}} </span>
                                        </div>
                                    </div>
                                    <a class="row my-1 hoverable-purple py-1 font-weight-bold"
                                       :href="'/shop/'+d.shop_id">
                                        <span class="   rounded-end    text-primary small   px-2 ">  {{getShop(d.shop_id)}} </span>
                                    </a>
                                    <div class="card-divider"></div>
                                    <div class="row">
                                        <div class="col">
                                            <span :class="d.discount_price != d.price? ' text-decoration-line-through ':''"
                                                  class="   rounded   bg-secondary text-white small   px-2 ">  {{getPrice(d.price)}} </span>
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div v-if="d.discount_price != d.price" class="col">
                                            <span class="  rounded  bg-primary text-white small   px-2 ">{{getPrice(d.discount_price)}}</span>
                                        </div>
                                    </div>


                                    <div class="card-divider"></div>
                                </div>


                            </div>
                            <div class="m-card-foote  bg-transparent position-relative w-100 py-4">
                                <img class="   back-footer-im position-absolute w-100 top-0"
                                     :src="assetLink+'/card-footer.png'"
                                     alt="" style=" ">
                                <!--<div class="  d-flex justify-content-center px-1  position-absolute bottom-1 z-index-3 w-100 text-center">-->

                                <!--<a class="btn bg-transparent py-2    text-white   px-1  w-100 move-on-hover hoverable"-->
                                <!--:href="'/product/'+d.name+'/'+d.id">-->

                                <!--جزییات</a>-->
                                <!--</div>-->
                            </div>

                        </a>
                    </div>
                    <h4 v-if=" noData" class="text-center mt-3 text-primary ">
                        نتیجه ای یافت نشد

                    </h4>

                </div>
            </div>
            <h4 v-if=" noData" class="text-center mt-3 text-primary">
                نتیجه ای یافت نشد

            </h4>
            <!--<paginator class="   my-1  " ref="paginator"></paginator>-->
            <div class="progress-line mt-1" :style="loading?'opacity:1;':'opacity:0;'"></div>

        </div>
    </div>

</template>

<script>

    let scrolled = false;
    let self;
    import paginator from '../components/pagination.vue';
    import slider from '../components/slider.vue';
    //    import {computed} from 'vue'
    import {Swiper, SwiperSlide} from 'swiper/vue';

    // Import Swiper styles
    import 'swiper/css';


    export default {
        props: ['admin', 'panel', 'dataLink', 'editLink', 'removeLink', 'shop-data', 'user-data', 'imgLink', 'assetLink', 'urlParams', 'type',],

        components: {paginator, slider, Swiper, SwiperSlide},
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
                shops: this.shopData ? JSON.parse(this.shopData) : [],
                users: this.userData ? JSON.parse(this.userData) : [],
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
//            this.getData();
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
            if (!this.params.shop) this.params.shop = "";
            if (!this.params.user) this.params.user = "";

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
            showDialog(type, message, click, params) {
                window.showDialog(type, message, onclick = () => click, params);
            },
            getShop(id) {
                let s = this.shops.find(function (el) {

                    return el.id == id;
                });

                if (s) return s.name; else return '';
            },
            getPrice(p) {

                if (p && p > 0)
                    return this.farsiDigit(this.separator(p)) + ' ت ';

                return 'نامشخص';
            },
            separator(price) {
                return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            farsiDigit(str) {
                let eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                let per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                for (let i in eng) {
//                    str = str.replaceAll(eng[i], per[i]);
                    let re = new RegExp(eng[i], "g");
                    str = str.replace(re, per[i]);
                }
                return str;

            },
            getLink(id, type) {

//                switch (type) {
//                    case 'pl':
                return '/product/' + id;
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

            getImage(docs) {
                //get profile for player and coach
                //get an env for club
                //get logo for shop
                if (!docs || docs.length === 0)
                    return this.assetLink + "/noimage.png";
                let doc;
//                switch (this.type) {
//                    case 'pl' || 'co':
                doc = docs.find(el => el.type_id == 4);
//                        break;
//                    case 'cl'  :
//                        doc = docs.find(el => el.type_id === 3);
//                        break;
//                    case 'sh'  :
//                        doc = docs.find(el => el.type_id === 6);
//                }

                if (doc)
                    return this.imgLink + "/" + doc.type_id + "/" + doc.id + ".jpg";
                else
                    return this.assetLink + "/noimage.png";
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
                    shop: this.params.shop ? this.params.shop : "",

                    price_h: this.$refs.priceSlider ? this.$refs.priceSlider.maxVal : null,
                    price_l: this.$refs.priceSlider ? this.$refs.priceSlider.minVal : null,

                    active: this.params.active !== null ? this.params.active : "",
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
