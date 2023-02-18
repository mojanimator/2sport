<template>

    <div class=" container-fluid  mx-sm-2  card shadow-lg   blur ">
        <div class="row text-primary justify-content-center  bg-light px-1   "
        >
            <div :class="admin?'col-md-6  ':'col-sm-6'">

                <select @change="getData(1)" class="px-4 my-2 form-control "

                        v-model="params.category"
                >
                    <option value="">همه دسته ها</option>
                    <option class="text-dark" v-for="category in categories"
                            :value="category.id">
                        {{ category.name }}

                    </option>
                </select>
            </div>

            <div :class="admin? 'col-md-6' :'col-sm-4 '" v-show="filter">
                <div class=" my-2   input-group  ">

                    <input id="name" type="text"
                           class="  px-4 form-control   "
                           v-model="params.name"
                           @keyup.enter="getData(1)" placeholder="کلمه کلیدی"
                           autofocus>
                    <!--<label for="search"-->
                    <!--class="col-md-12    form-label  text-md-right">نام بازیکن </label>-->


                </div>

            </div>
            <div v-if="admin" class="col-md-6  ">
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
                    <label class="btn btn-outline-primary px-1" for="active-all">همه</label>

                    <input @change="getData(1)" v-model="params.active" :value="1" type="radio" class="btn-check"
                           name="active"
                           id="active-active"
                           autocomplete="off"/>
                    <label class="btn btn-outline-primary px-1" for="active-active">فعال</label>

                    <input @change="getData(1)" v-model="params.active" :value="0" type="radio" class="btn-check"
                           name="active"
                           id="active-deactive"
                           autocomplete="off"/>
                    <label class="btn btn-outline-primary px-1" for="active-deactive">پیش نویس</label>
                </div>
            </div>


            <div v-if="admin && users.length>0" class="col-sm-4" v-show="filter">

                <select class="px-4 my-2  form-control "

                        v-model="params.user"
                        @change=" getData(1)">
                    <option value="">همه کاربران</option>
                    <option class="text-dark" v-for="user in users"
                            :value="user.id">
                        {{ user.username }}

                    </option>
                </select>


            </div>


            <div class="row mx-auto my-2">
                <paginator class="col-sm-6  my-1  " ref="paginator"></paginator>
                <button class="btn btn-secondary rounded col-sm-4 my-1" type="button"
                        id="name-addon"
                        @click="getData(1)">
                    جست و جو
                </button>
                <div class="col-sm-2 btn-group px-1 bg-transparent  my-1">
                    <button class="btn     px-2   " type="button"
                            :class="filter?'btn-primary' :'btn-outline-primary'"
                            title="فیلتر" data-toggle="tooltip" data-placement="bottom"
                            @click="filter=!filter">

                        <i class="fa fa-search" aria-hidden="true"></i>


                    </button>

                    <button v-if="!admin" class="btn     px-2   btn-outline-primary  " type="button"
                            @click="view=='horizontal'? view='vertical' :view='horizontal'"
                            title="نمایش" data-toggle="tooltip" data-placement="bottom">
                        <span v-if="view=='horizontal'">
                        <i class="fa fa-list-ul" aria-hidden="true"></i>
                         </span>
                        <span v-if="view=='vertical'">
                            <i class="fa fa-th" aria-hidden="true"></i>
                        </span>

                    </button>
                </div>
            </div>
        </div>


        <div class="      shadow-card ">

            <div
                class=" col-12 mx-auto  mb-3 row     font-weight-bold text-primary border border-2 border-primary rounded bg-light p-2 mb-2 z-index-0 text-center ">
                <div class="col-12 px-0 hover-underline small " :class="params.order_by=='created_at'?'active':''"
                     @click="params.order_by='created_at';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC';  ;getData(1);">
                    تاریخ ثبت
                </div>

            </div>

            <div v-show="loading" class="  position-absolute col-12 mx-auto  text-center" style="margin-top: -1rem">
                <div v-for="i in  [1,1,1,1,1]"
                     class="spinner-grow mx-1   spinner-grow-sm text-primary "
                     role="status">
                    <span class="visually-hidden"> </span>
                </div>

            </div>

            <div :class=" admin || view=='horizontal'?'': 'card-container'" class="my-4">


                <!--admin panel-->
                <div v-if=" admin" class="row mx-1 ">
                    <div v-for="d,idx in data" class="col-md-6 col-xl-4 px-0 px-sm-1  position-relative ">
                        <div class="position-absolute end-0 top-0     "
                             style="z-index: 2"
                        >
                              <span class=" rounded mx-1  "
                                    :class="d.active? 'bg-success hoverable-primary':'bg-dark   hoverable-success'"
                                    @click="  showDialog('confirm','از '+(d.active?'غیر فعال سازی':'فعال سازی')+' اطمینان دارید؟' , activate ,{'id':d.id,'active':!d.active,})">
                                 <i class="fa fa-check-circle  text-white   mx-2 "
                                    aria-hidden="true"></i>
                            </span>
                            <span class="bg-danger rounded   hoverable-primary  "

                                  @click="  showDialog('confirm','از حذف اطمینان دارید؟', remove ,d.id)">
                                 <i class="fa   fa-trash  text-white   mx-2 "
                                    aria-hidden="true"></i>
                            </span>


                        </div>
                        <a :href="'/panel/blog/edit/'+d.id" class="m-1 d-block  ">
                            <div class="card move-on-hover">
                                <div class="card-body  p-3 ">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <div class="small  mb-0 text-primary font-weight-bold">
                                                    {{
                                                        d.title && d.title.length > 30 ? d.title.substring(0, 30) + '...' : d.title
                                                    }}
                                                </div>
                                                <div
                                                    class="small text-secondary text-opacity-75 font-weight-bolder  my-1">
                                                    {{ getCategory(d.category_id) }}

                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-4 text-end  " style="height: 5rem !important;">

                                            <img :src="getImage(d.docs )" class="rounded  w-100 h-100 "
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
                <div v-if=" !admin && view=='horizontal'" class="row mx-1 ">
                    <div v-for="d,idx in data" class="col-md-6 col-xl-4 px-0 px-sm-1  position-relative ">

                        <a :href="'/blog/'+d.id" class="m-1 d-block  ">
                            <div class="card move-on-hover">
                                <div class="card-body  p-3 ">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <div class="small  mb-0 text-primary font-weight-bold">
                                                    {{
                                                        d.title && d.title.length > 30 ? d.title.substring(0, 30) + '...' : d.title
                                                    }}
                                                </div>
                                                <div
                                                    class="small text-secondary text-opacity-75 font-weight-bolder  my-1">
                                                    {{ getCategory(d.category_id) }}

                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-4 text-end  " style="height: 5rem !important;">

                                            <img :src="getImage(d.docs )" class="rounded  w-100 h-100 "
                                                 style="object-fit: contain"
                                                 alt="" @error="imgError">


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div v-if=" !admin && view=='vertical'" class="row mx-1 ">
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
                            <div
                                class="m-card-body  px-2 d-flex  flex-column  align-self-stretch  justify-content-between  text-end z-index-1">

                                <div class="text-primary  my-2 text-center max-2-line ">
                                    {{
                                        d.title && d.title.length > 30 ? d.title.substring(0, 30) + '...' : d.title
                                    }}
                                </div>

                                <div class=" text-center    pt-1 ">
                                    <div class="row">
                                        <div class="col">

                                            <span
                                                class="   rounded-end  bg-secondary text-white small   px-2 ">  {{ getCategory(d.category_id) }} </span>
                                        </div>
                                        <Swiper v-if="d.tags" class="w-100 my-1"
                                                :slides-per-view="d.tags.split(' ').length>1?2:'auto'"
                                                :centeredSlides="false"
                                                :grabCursor="false"
                                                :space-between="30"
                                                :freeMode="true"
                                        >
                                            <swiper-slide v-for="s in d.tags.split(' ')"
                                                          class="   rounded   bg-primary text-white small p-1  mx-1 ">
                                                {{ s }}
                                            </swiper-slide>
                                        </Swiper>
                                    </div>


                                    <div class="card-divider"></div>


                                    <p class=" small text-primary text-start text-lg">
                                        <i class="fas  fa-map-marker-alt"></i>
                                        <span class="mx-1 text-danger">  {{ getCategory(d.category_id) }}  </span>

                                    </p>
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
                </div>
                <h4 v-if=" noData" class="text-center mt-3 text-primary">
                    نتیجه ای یافت نشد

                </h4>


            </div>


        </div>
    </div>

</template>

<script>


import paginator from '../components/pagination.vue';
import slider from '../components/slider.vue';
import {Swiper, SwiperSlide} from 'swiper/vue';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';


export default {
    props: ['admin', 'dataLink', 'editLink', 'removeLink', 'user-data', 'category-data', 'imgLink', 'assetLink', 'urlParams', 'type',],

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
            categories: JSON.parse(this.categoryData),
            users: this.userData ? JSON.parse(this.userData) : [],
            filter: false,
            pinSearch: false,
            view: 'horizontal',
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
        this.getData();
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
        if (!this.params.category) this.params.category = "";

        if (!this.params.user) this.params.user = "";
    }, methods: {


        getCategory(id) {
            let s = this.categories.find(function (el) {

                return el.id == id;
            });

            if (s) return s.name; else return '';
        },

        getLink(id, type) {

//                switch (type) {
//                    case 'pl':
            return '/blog/' + id;
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
            doc = docs.find(el => el.type_id == 7);
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
        setEvents(el) {

        },
        activate(data) {
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
        }, remove(id) {
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


//                this.loading.removeClass('hide');
            this.loading = true;
            this.noData = false;


            this.params = {
                name: this.params.name ? this.params.name : this.name,
                page: !isNaN(this.params.page) ? this.params.page : this.page,
                paginate: this.params.paginate ? this.params.paginate : this.paginate,
                order_by: this.params.order_by ? this.params.order_by : 'created_at',
                dir: this.params.dir ? this.params.dir : 'DESC',
                sport: this.params.sport ? this.params.sport : "",
                province: this.params.province ? this.params.province : "",
                county: this.params.county ? this.params.county : "",
                category: this.params.category ? this.params.category : "",

                active: this.params.active !== null ? this.params.active : "",
                user: this.params.user ? this.params.user : "",
                panel: this.admin,
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
                            this.data = response.data.data;

                            this.total = response.data.total;
//                                this.page = response.data.current_page + 1;

                            this.loading = false;
                            if (this.data.length === 0)
                                this.noData = true;
                            if (this.params.page > 1 && this.data.length === 0) {
                                this.noData = false;
                                this.params.page = 1;
                                this.getData();
                            }
                            this.pagination =
                                {
                                    current_page: response.data.current_page,
                                    first_page_url: response.data.first_page_url,
                                    next_page_url: response.data.next_page_url,
                                    prev_page_url: response.data.prev_page_url,
                                    last_page_url: response.data.last_page_url,
                                    last_page: response.data.last_page,
                                    from: response.data.from,
                                    to: response.data.to,
                                    total: response.data.total,
                                };
                            this.$refs.paginator.updatePaginator(this.pagination);
//
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
