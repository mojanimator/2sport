<template>

    <div class="       shadow-lg   blur ">
        <div class="row mx-auto    text-primary justify-content-center  bg-light     "
        >
            <div :class="admin?'col-md-6  ':'col-sm-6'">

                <select @change="getData(1)" class="px-4 my-2 form-control "

                        v-model="params.category"
                >
                    <option value="">همه ورزش ها</option>
                    <option class="text-dark" v-for="sport,idx in sports"
                            :value=" idx">
                        {{ sport.name }}

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
                    <label class="btn btn-outline-primary" for="active-deactive">غیر فعال</label>
                </div>
            </div>


            <div class="row mx-auto  my-2">
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


        <div class="       shadow-card ">

            <div class="     mb-3       font-weight-bold text-primary border border-2 border-primary rounded bg-light p-2 mb-2 z-index-0 text-center ">
                <div class="  hover-underline " :class="params.order_by=='updated_at'?'active':''"
                     @click="params.order_by='updated_at';if (params.dir=='DESC')params.dir ='ASC';else params.dir ='DESC';  ;getData(1);">
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

            <div :class=" panel || view=='horizontal'?'': 'card-container'" class="my-4">


                <!--admin panel-->
                <div v-if="panel && admin" class="row mx-1 ">
                    <div v-for="d,idx in data" class="col-md-6 col-xl-4 px-0 px-sm-1  position-relative ">
                        <div class="position-absolute end-0 top-0     "
                             style="z-index: 2"
                        >
                              <span class=" rounded mx-1   p-1 " :title="d.active? 'غیر فعال سازی':'فعال سازی'"
                                    :class="d.active? 'bg-success hoverable-primary':'bg-dark   hoverable-success'"
                                    @click="  showDialog('confirm','از '+(d.active?'غیر فعال سازی':'فعال سازی')+' اطمینان دارید؟' , activate ,{'id':d.id,'active':!d.active,})">
                                 <i class="fa    text-white   mx-2 "
                                    :class="d.active? 'fa-check-circle':'fa-minus-circle'"
                                    aria-hidden="true"></i>
                            </span>
                            <span class="bg-danger rounded   p-1 hoverable-primary  "

                                  @click="  showDialog('confirm','از حذف اطمینان دارید؟', remove ,d.id)">
                                 <i class="fa   fa-trash  text-white   mx-2 "
                                    aria-hidden="true"></i>
                            </span>


                        </div>
                        <a :href="'/panel/table/edit/'+d.id" class="m-1 d-block  ">
                            <div class="card move-on-hover ">
                                <div class="card-body  px-1 pt-4 pb-2 ">
                                    <div class="row">
                                        <div class="col-4 text-end  " style="height: 5rem !important;">

                                            <img :src="getImage(d.tournament_id)" class="rounded  w-100 h-100 "
                                                 style="object-fit: contain"
                                                 alt="" @error="imgError">


                                        </div>
                                        <div class="col-8">
                                            <div class="numbers">
                                                <div v-if="d.tournament_id"
                                                     class="small  mb-0 text-primary font-weight-bold">
                                                    {{ getTournament(d.tournament_id)}}

                                                </div>
                                                <div class="small  mb-0 text-primary font-weight-bold">
                                                    {{d.title }}

                                                </div>
                                                <div class="small text-secondary text-opacity-75 font-weight-bolder  my-1">
                                                    {{ getSport(d.type_id)}}

                                                </div>
                                                <div class="small text-blue text-opacity-75 font-weight-bolder  my-1">
                                                    {{ d.tags != null ? d.tags.split(' ').join(', ') : ''}}

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
        props: ['panel', 'admin', 'dataLink', 'editLink', 'removeLink', 'tournamentData', 'sportData', 'imgLink', 'assetLink', 'urlParams', 'type',],

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
                params: {order_by: 'updated_at', dir: 'DESC'},
                sports: this.sportData ? JSON.parse(this.sportData) : [],
                tournaments: this.tournamentData ? JSON.parse(this.tournamentData) : [],
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


        }, methods: {


            getSport(id) {
                let s = this.sports.find(function (el) {

                    return el.id == id;
                });

                if (s) return s.name; else return '';
            },


            getTournament(id) {
                let s = this.tournaments.find(function (el) {

                    return el.id == id;
                });

                if (s) return s.name; else return '';
            },


            showDialog(type, message, click, params) {
                window.showDialog(type, message, onclick = () => click, params);

            },
            log(str) {
                console.log(str);
            },
            getImage(id) {

                return this.imgLink + '/' + id + '.jpg';
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
//                            console.log(response);
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
                    order_by: this.params.order_by ? this.params.order_by : 'updated_at',
                    dir: this.params.dir ? this.params.dir : 'DESC',
                    sport: this.params.sport ? this.params.sport : "",
                    category: this.params.category ? this.params.category : "",

                    active: this.params.active !== null ? this.params.active : "",
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
