<template>

    <div class=" container-fluid  mx-sm-2  card shadow-lg   blur ">
        <div class="row text-primary justify-content-center  bg-light px-1   "
        >
            <div :class="admin?'col-md-6  ':'col-sm-6'">

                <select @change="getData(1)" class="px-4 my-2 form-control "

                        v-model="params.sport"
                >
                    <option value="">همه دسته ها</option>
                    <option class="text-dark" v-for="category in sports"
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


            <div class="row col-12 mx-auto justify-content-center  my-2  ">

                <button class="btn btn-secondary rounded col-sm-4   my-1" type="button"
                        id="name-addon"
                        @click="getData(1)">
                    جست و جو
                </button>
                <div class="col-sm-2  btn-group px-1 bg-transparent  my-1">
                    <button class="btn     px-2   " type="button"
                            :class="filter?'btn-primary' :'btn-outline-primary'"
                            title="فیلتر" data-toggle="tooltip" data-placement="bottom"
                            @click="filter=!filter">

                        <i class="fa fa-search" aria-hidden="true"></i>


                    </button>

                    <!--<button v-if="!admin" class="btn     px-2   btn-outline-primary  " type="button"-->
                    <!--@click="view=='horizontal'? view='vertical' :view='horizontal'"-->
                    <!--title="نمایش" data-toggle="tooltip" data-placement="bottom">-->
                    <!--<span v-if="view=='horizontal'">-->
                    <!--<i class="fa fa-list-ul" aria-hidden="true"></i>-->
                    <!--</span>-->
                    <!--<span v-if="view=='vertical'">-->
                    <!--<i class="fa fa-th" aria-hidden="true"></i>-->
                    <!--</span>-->

                    <!--</button>-->
                </div>
            </div>
        </div>


        <div class="      shadow-card ">

            <div class=" col-12 mx-auto  mb-3 row     font-weight-bold text-primary border border-2 border-primary rounded bg-light p-2 mb-2 z-index-0 text-center ">
                <div class="col-12 px-0 hover-underline small " :class="params.order_by=='updated_at'?'active':''"
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

            <div :class=" admin || view=='horizontal'?'': 'card-container'" class="my-4">


                <!--admin panel-->
                <div class="row mx-1 ">
                    <div v-for="d,idx in data" class="col-md-6 col-xl-4 px-0 px-sm-1  position-relative ">
                        <div v-if=" admin" class="position-absolute end-0 top-0     "
                             style="z-index: 2"
                        >

                            <span class="bg-danger rounded   p-1 hoverable-primary  "

                                  @click="  showDialog('confirm','از حذف اطمینان دارید؟', remove ,d.id)">
                                 <i class="fa   fa-trash  text-white   mx-2 "
                                    aria-hidden="true"></i>
                            </span>


                        </div>
                        <a :href="admin ?'/panel/event/edit/'+d.id : '/event/'+d.id" class="m-1 d-block  ">
                            <div class="card move-on-hover">
                                <div class="card-body  p-3 ">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="numbers">
                                                <div class="  text-center  mb-0 text-primary font-weight-bold mt-1">
                                                    {{d.title && d.title.length > 30 ? d.title.substring(0, 30) + '...' : d.title
                                                    }}
                                                </div>
                                                <hr class="bg-danger border-1 border-top border-primary my-1">
                                                <div class="d-flex flex-row   mb-2"
                                                     :class="d.team1!=null && d.team2 !=null? 'justify-content-around':'justify-content-center'">
                                                    <div v-if="d.team1!=null"
                                                         class="small  mb-0 text-indigo font-weight-bold">
                                                        {{d.team1 && d.team1.length > 30 ? d.team1.substring(0, 30) + '...' : d.team1
                                                        }}
                                                    </div>
                                                    <span v-if="d.team1!=null && d.team2 !=null"
                                                          class="bg-danger border-start border-primary opacity-30"></span>
                                                    <div v-if="d.team2!=null"
                                                         class="small  mb-0 text-purple font-weight-bold">
                                                        {{d.team2 && d.team2.length > 30 ? d.team2.substring(0, 30) + '...' : d.team2
                                                        }}
                                                    </div>
                                                </div>
                                                <div class="small position-absolute px-2 top-0 start-0 text-secondary text-opacity-75   border border-1 rounded-3   my-1">
                                                    {{ getCategory(d.sport_id)}}

                                                </div>
                                                <div class="text-center">
                                                    <hr class="bg-danger border-1 border-top border-primary   my-1">
                                                    <div class="d-flex flex-row "
                                                         :class="d.link!=null? 'justify-content-between':'justify-content-center'">
                                                        <div class="  small ">{{getDateTime(d.time)}}</div>
                                                        <a v-if="d.link!=null" :href="d.link"
                                                           class="small d-inline-block px-2   hoverable-text-cyan  mt-0 bg-gradient-primary text-white rounded-3  ">
                                                            {{d.source && d.source.length > 30 ? d.source.substring(0, 30) + '...' : d.source
                                                            }}
                                                        </a>

                                                    </div>
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


    // Import Swiper styles


    export default {
        props: ['admin', 'dataLink', 'editLink', 'removeLink', 'user-data', 'sport-data', 'imgLink', 'assetLink', 'urlParams', 'type',],

        components: {},
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
                sports: JSON.parse(this.sportData),
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
            if (!this.params.sport) this.params.sport = "";

            if (!this.params.user) this.params.user = "";
        }, methods: {

            getDateTime(date) {
                let d = new Date(date * 1000);

                let options = {
                    hour12: false,
                    weekday: 'long',
                    year: 'numeric',
                    month: 'numeric',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    nu: 'arab',
                    calendar: 'persian',
                };
//                let day = d.toLocaleDateString('fa-IR');

                return d.toLocaleString('fa-IR', options).split(' ').reverse().join(' ');
            },
            getCategory(id) {
                let s = this.sports.find(function (el) {

                    return el.id == id;
                });

                if (s) return s.name; else return '';
            },

            getLink(id, type) {

//                switch (type) {
//                    case 'pl':
                return '/event/' + id;
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
                    order_by: this.params.order_by ? this.params.order_by : 'updated_at',
                    dir: this.params.dir ? this.params.dir : 'DESC',
                    sport: this.params.sport ? this.params.sport : "",


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
