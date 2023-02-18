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
            <swiper-slide title="فیلتر" class="w-auto my-auto text-secondary"><i class="fa fa-filter"
                                                                                 aria-hidden="true"></i>
            </swiper-slide>

            <swiper-slide style="width: 8rem">
                <div class="    input-group   my-2">

                    <input id="name" type="text"
                           class="  px-4 form-control   "
                           v-model="params.name"
                           @keyup.enter="getData(1)" placeholder="نام نمایندگی"
                           autofocus>


                </div>
            </swiper-slide>


            <swiper-slide style="width: 8rem">
                <select class="px-4 my-2  form-control "

                        v-model="params.province"
                        @change="params.county='';getData(1)">
                    <option value=""> استان</option>
                    <option class="text-dark" v-for="province in provinces"
                            :value="province.id">
                        {{ province.name }}

                    </option>
                </select>
            </swiper-slide>

            <swiper-slide style="width: 8rem">
                <select class="px-4 my-2  form-control "

                        v-model="params.county"
                        @change=" getData(1) ">
                    <option value=""> شهر</option>
                    <option class="text-dark"
                            v-for="county in counties.filter(function(el) {return params.province==el.province_id;} )"
                            :value="county.id">
                        {{ county.name }}

                    </option>
                </select>
            </swiper-slide>
            <swiper-slide v-show="admin && users.length>0" style="width: 8rem">
                <select class="px-4 my-2  form-control "

                        v-model="params.user"
                        @change=" getData(1)">
                    <option value=""> مالک</option>
                    <option class="text-dark" v-for="user in users"
                            :value="user.id">
                        {{ user.name + ' ' + user.family }}

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


        </Swiper>

        <div class="        shadow-card  " style="margin-top: 4.5rem">

            <div class=" col-12 mx-auto  mb-3 row     font-weight-bold text-primary border border-2 border-primary rounded bg-light p-2 mb-2 z-index-0 text-center ">
                <div class="col-3 px-0 hover-underline small " :class="params.order_by=='created_at'?'active':''"
                     @click="params.order_by='created_at';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC';  ;getData(1);">
                    تاریخ ثبت
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
                              <span class=" rounded mx-1   p-1 " :title="d.active? 'غیر فعال سازی':'فعال سازی'"
                                    :class="d.active? 'bg-success hoverable-primary':'bg-dark   hoverable-success'"
                                    @click="toggleActivate({'id':d.id,'active': d.active})">
                                 <i class="fa   text-white   mx-2 "
                                    :class="d.active? 'fa-check-circle':'fa-minus-circle'"
                                    aria-hidden="true"></i>
                            </span>
                            <span class="bg-danger rounded   p-1 hoverable-primary  "

                                  @click="  showDialog('confirm','از حذف اطمینان دارید؟', remove ,d.id)">
                                 <i class="fa   fa-trash  text-white   mx-2 "
                                    aria-hidden="true"></i>
                            </span>


                        </div>
                        <a :href="'/panel/agency/edit/'+d.id" class="m-1 d-block  ">
                            <div class="card move-on-hover">
                                <div class="card-body  p-3 ">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <div class="  mb-1 text-primary font-weight-bold">
                                                    {{d.name }}
                                                </div>
                                                <div class="small text-secondary text-opacity-75     ">
                                                    {{ d.phone }}

                                                </div>
                                                <div class="small  text-opacity-75   ">
                                                    {{getProvinceCounty(d.province_id, d.county_id)}}

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end  " style="height: 5rem !important;">

                                            <div class="  "><i class="fa fa-3x fa-house-user text-primary m-1"
                                                               aria-hidden="true"></i></div>


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
        props: ['price', 'admin', 'panel', 'dataLink', 'editLink', 'removeLink', 'user-data', 'province-data', 'county-data', 'imgLink', 'assetLink', 'urlParams', 'type',],

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
                provinces: JSON.parse(this.provinceData),
                counties: JSON.parse(this.countyData),

                users: this.userData ? JSON.parse(this.userData) : [],
                filter: false,
                pinSearch: false,
                view: 'horizontal',
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

            if (!this.params.province) this.params.province = "";
            if (!this.params.county) this.params.county = "";
            if (!this.params.user) this.params.user = "";
        }, methods: {
            toggleActivate(d) {
                this.showDialog('confirm', 'از ' + (d.active ? 'غیر فعال سازی' : 'فعال سازی') + ' اطمینان دارید؟', this.edit, {
                    'id': d.id,
                    'active': !d.active
                });

            },


            getProvinceCounty(pId, cId) {

                let p = this.provinces.find((e) => e.id == pId);
                let c = this.counties.find((e) => e.id == cId);
                if (p || c)
                    return (p && p.name ? ( p.name + (c && c.name ? " _ " : '')) : '') + (c && c.name ? c.name : '');
                else
                    return 'نامشخص'
            },
            getLink(id, type) {

//                switch (type) {
//                    case 'pl':
                return '/agency/' + id;
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

                let doc;
//                switch (this.type) {
//                    case 'pl' || 'co':
                doc = docs.find(el => el.type_id == 1);
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
                    province: this.params.province ? this.params.province : "",
                    county: this.params.county ? this.params.county : "",

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
