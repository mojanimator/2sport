<template>

    <div class=" container-fluid    card shadow-lg   blur ">
        <div class="row rounded m-2 text-primary  justify-content-center  bg-light px-1   "
             :class="pinSearch==true?'sticky-top':''">

            <div class=" col-md-6 " v-show="!minimize">
                <div class=" my-2   input-group  ">

                    <input id="name" type="text"
                           class="  px-4 form-control   "
                           v-model="params.name"
                           @keyup.enter="getData(1)" placeholder="نام فروشگاه"
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
                    <label class="btn btn-outline-primary" for="active-all">همه</label>

                    <input @change="getData(1)" v-model="params.active" :value="1" type="radio" class="btn-check"
                           name="active"
                           id="active-active"
                           autocomplete="off"/>
                    <label class="btn btn-outline-primary" for="active-active">فعال</label>

                    <input @change="getData(1)" v-model="params.active" :value="0" type="radio" class="btn-check"
                           name="active"
                           id="active-deactive"
                           autocomplete="off"/>
                    <label class="btn btn-outline-primary" for="active-deactive">غیرفعال</label>
                </div>
            </div>


            <div class="col-sm-4" v-show="!minimize">

                <select class="px-4 my-2  form-control "

                        v-model="params.province"
                        @change="params.county='';getData(1)">
                    <option value="">همه استان ها</option>
                    <option class="text-dark" v-for="province in provinces"
                            :value="province.id">
                        {{ province.name }}

                    </option>
                </select>


            </div>
            <div class="col-sm-4" v-show="!minimize">

                <select class="px-4 my-2  form-control "

                        v-model="params.county"
                        @change=" getData(1) ">
                    <option value="">همه شهر ها</option>
                    <option class="text-dark"
                            v-for="county in counties.filter(function(el) {return params.province==el.province_id;} )"
                            :value="county.id">
                        {{ county.name }}

                    </option>
                </select>
            </div>
            <div v-if="admin" class="col-sm-4" v-show="!minimize">

                <select class="px-4 my-2  form-control "

                        v-model="params.user"
                        @change=" getData(1)">
                    <option value="">همه کاربران</option>
                    <option class="text-dark" v-for="user in users"
                            :value="user.id">
                        {{ user.name }}

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
                    <button class="btn btn-primary    px-2   " type="button"
                            title="ساده/پیشرفته" data-toggle="tooltip" data-placement="bottom"
                            @click="minimize=!minimize">
                        <span v-show="!minimize">
                        <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
                        </span>
                        <span v-show="minimize">
                        <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
                        </span>
                    </button>

                    <button class="btn     px-2     " type="button"
                            :class="pinSearch?'btn-primary': 'btn-outline-primary'" @click="pinSearch=!pinSearch"
                            title="چسباندن به بالای صفحه" data-toggle="tooltip" data-placement="bottom">
                        <span>
                        <i class="fa fa-eye" aria-hidden="true"></i>
                        </span>

                    </button>
                </div>
            </div>
        </div>


        <div class="  container-fluid       shadow-card ">

            <div class=" col-12 mx-auto  mb-3 row     font-weight-bold text-primary border border-2 border-primary rounded bg-light p-2 mb-2 z-index-0 text-center ">
                <!--<div class="col-6 hover-underline "-->
                <!--:class="params.order_by=='created_at' &&  params.dir =='DESC' ?'active':''"-->
                <!--@click="params.order_by='created_at';   params.dir ='DESC';   getData(1);">-->
                <!--جدیدترین-->
                <!--</div>-->
                <div class="col-6 hover-underline " :class="params.order_by=='created_at'?'active':''"
                     @click="params.order_by='created_at';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC';  ;getData(1);">
                    تاریخ ثبت
                </div>
                <div class="col-6 hover-underline "
                     :class="params.order_by=='born_at'  ?'active':''"
                     @click="params.order_by='born_at';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC' ;getData(1);">
                    سن
                </div>

            </div>
            <div v-show="loading" class="  position-absolute col-12 mx-auto  text-center" style="margin-top: -1rem">
                <div v-for="i in  [1,1,1,1,1]"
                     class="spinner-grow mx-1   spinner-grow-sm text-primary "
                     role="status">
                    <span class="visually-hidden"> </span>
                </div>

            </div>

            <div :class=" admin? '': 'card-container'" class="my-4">

                <!--admin panel-->
                <div v-if=" admin" class="row mx-1 ">
                    <div v-for="d,idx in data" class="col-md-6   position-relative ">
                        <div class="position-absolute end-0 top-0     "
                             style="z-index: 2"
                        >
                              <span class=" rounded mx-1   p-1 "
                                    :class="d.active? 'bg-success hoverable-primary':'bg-dark   hoverable-success'"
                                    @click="  showDialog('confirm','از '+(d.active?'غیر فعال سازی':'فعال سازی')+' اطمینان دارید؟' , edit ,{'id':d.id,'active':!d.active})">
                                 <i class="fa fa-check-circle  text-white   mx-2 "
                                    aria-hidden="true"></i>
                            </span>
                            <span class="bg-danger rounded   p-1 hoverable-primary  "

                                  @click="  showDialog('confirm','از حذف اطمینان دارید؟', remove ,d.id)">
                                 <i class="fa   fa-trash  text-white   mx-2 "
                                    aria-hidden="true"></i>
                            </span>


                        </div>
                        <a :href="'/panel/shop/edit/'+d.id" class="m-1 d-block  ">
                            <div class="card move-on-hover">
                                <div class="card-body  p-3 ">
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
                                                    {{ }}

                                                </div>
                                                <div class="small  text-opacity-75 font-weight-bolder  my-1">
                                                    {{getProvinceCounty(d.province_id, d.county_id)}}

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
                <div v-else="" class="row mx-1">
                    <div v-for="d,idx in data" class=" card-width   mb-1   ">
                        <a :href="getLink(d.id, type)"
                           class="m-card  d-flex align-items-start align-content-around flex-column    "
                           data-toggle="modal"
                           :key="d.id">
                            <div class="m-card-heade bg-transparent  w-100 ">

                                <div class="d-flex justify-content-between position-relative    mx-1"
                                     style="z-index: 3;">

                                    <div class="position-absolute    text-white m-2  small-1"
                                         data-toggle="tooltip"
                                         data-placement="top"
                                         title="جنسیت">
                                        <i v-if="d.is_man" class="fa fa-male fa-2x" aria-hidden="true"></i>
                                        <i v-else="" class="fa fa-female fa-2x" aria-hidden="true"></i>
                                    </div>

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

                                <div class="text-primary  my-2 text-center max-2-line "> {{d.name + ' ' + d.family}}
                                </div>

                                <div class=" text-center     pt-1 ">
                                    <div class="row">
                                        <div class="col">
                                            <span class="  rounded-start   bg-primary text-white small   px-2 ">{{this.getSport(d.sport_id)}}</span>
                                            <span class="   rounded-end  bg-secondary text-white small   px-2 ">  {{d.id}} </span>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <span class="  rounded-start   bg-primary text-white small   px-2 ">سن</span>
                                            <span class="   rounded-end  bg-secondary text-white small   px-2 ">  {{getAge(d.born_at)}} </span>
                                        </div>

                                    </div>


                                    <div class="card-divider"></div>


                                    <p class=" small text-primary text-start text-lg">
                                        <i class="fas  fa-map-marker-alt"></i>
                                        <span class="mx-1 text-danger">  {{getProvinceCounty(d.province_id, d.county_id)}}  </span>

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
    //    import {computed} from 'vue'


    export default {
        props: ['admin', 'dataLink', 'editLink', 'removeLink', 'user-data', 'province-data', 'county-data', 'sport-data', 'imgLink', 'assetLink', 'urlParams', 'type',],

        components: {paginator, slider},
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
                provinces: JSON.parse(this.provinceData),
                counties: JSON.parse(this.countyData),
                sports: JSON.parse(this.sportData),
                users: this.userData ? JSON.parse(this.userData) : [],
                minimize: false,
                pinSearch: false,
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

            if (!this.params.province) this.params.province = "";
            if (!this.params.county) this.params.county = "";
            if (!this.params.user) this.params.user = "";
        }, methods: {


            getProvinceCounty(pId, cId) {
                let p = this.provinces.find((e) => e.id == pId);
                let c = this.counties.find((e) => e.id == cId);
                if (p && c)
                    return p.name + " _ " + c.name;
                else
                    return 'نامشخص'
            },
            showDialog(type, message, click, params) {
                window.showDialog(type, message, onclick = () => click, params);


            },
            getLink(id, type) {

//                switch (type) {
//                    case 'pl':
                return '/shop/' + id;
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
                doc = docs.find(el => el.type_id == 6);
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
