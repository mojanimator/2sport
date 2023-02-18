<template>
    <div class="container  ">
        <transition :duration="1000" name="fade" style="transition: opacity 1.0s ease">

            <div class="row ">
                <div class="  navbar-light bg-light position-absolute start-0 end-0 "></div>


                <nav id="navbars" class="navbar navbar-expand navbar-light bg-light   "
                     aria-label="news">
                    <div class=" container-fluid   ">
                        <!--<a class="navbar-brand me-0 me-md-2 font-weight-bold" href="/">-->
                        <!--<img src="/img/icon.png" height="64" alt="">-->

                        <!--</a>-->
                        <!--<button class="navbar-toggler  collapsed" type="button" data-bs-toggle="collapse"-->
                        <!--data-bs-target="#navbar2" aria-controls="navbar1" aria-expanded="false"-->
                        <!--aria-label="Toggle navigation">-->
                        <!--<span class="navbar-toggler-icon"></span>-->
                        <!--</button>-->

                        <div class="navbar-collapse collapse row " id="navbar2" style="">
                            <ul class="navbar-nav col-md-7  justify-content-center   ">

                                <li class="nav-item small ms-2  my-1">
                                    <div class="nav-link px-1 px-md-2  hoverable-primary rounded font-weight-bold "
                                         :class="view=='blog-all'? 'text-white bg-primary':'text-primary'"
                                         aria-current="page" @click="view='blog-all'">اخبار ورزشی
                                    </div>
                                </li>
                                <li class="nav-item   small ms-2 ms-sm-1 ms-md-1 my-1">
                                    <div class="nav-link px-1 px-md-2  hoverable-primary rounded  font-weight-bold "
                                         :class="view=='blog-iran'? 'text-white bg-primary':'text-primary'"
                                         aria-current="page" @click="view='blog-iran'">اخبار فوتبال داخلی
                                    </div>
                                </li>
                                <li class="nav-item  small ms-2 ms-sm-1 ms-md-1 my-1">
                                    <div class="nav-link px-1 px-md-2  hoverable-primary rounded font-weight-bold  "
                                         :class="view=='blog-world'? 'text-white bg-primary':'text-primary'"
                                         aria-current="page" @click="view='blog-world'">اخبار فوتبال خارجی
                                    </div>
                                </li>
                            </ul>
                            <ul class="navbar-nav  col-md-5    justify-content-center justify-content-md-start">
                                <li class="nav-item  small ms-2 ms-sm-1 ms-md-1 my-1">
                                    <a class="nav-link px-1 px-md-2  hoverable-primary rounded font-weight-bold  "
                                       :class="view=='table-all'? 'text-white bg-primary':'text-primary'"
                                       aria-current="page" @click="view='table-all'">جدول لیگ ها</a>
                                </li>
                                <li class="nav-item  small ms-2 ms-sm-1 ms-md-1 my-1">
                                    <div class="nav-link px-1 px-md-2  hoverable-primary rounded font-weight-bold  "
                                         :class="view=='conductor'? 'text-white bg-primary':'text-primary'"
                                         aria-current="page" @click="view='conductor'">کنداکتور ورزشی
                                    </div>
                                </li>
                                <li class="nav-item  small ms-2 ms-sm-1 ms-md-1 my-1"
                                    @click="openLink('videos')">
                                    <div class="nav-link px-1 px-md-2  hoverable-primary rounded font-weight-bold  "
                                         :class="view=='video'? 'text-white bg-primary':'text-primary'"
                                         aria-current="page" @click="view='conductor'">ویدیو
                                    </div>
                                </li>


                            </ul>

                        </div>
                    </div>
                </nav>

                <section class="row mt-1 flex-wrap " data-masonry='{"percentPosition": true }'>
                    <!--<div v-show="loading" class="  position-absolute col-12 mx-auto  text-center"-->
                    <!--style="margin-top: -1rem">-->
                    <!--<div v-for="i in  [1,1,1,1,1]"-->
                    <!--class="spinner-grow mx-1 mt-3   spinner-grow-sm text-primary "-->
                    <!--role="status">-->
                    <!--<span class="visually-hidden"> </span>-->
                    <!--</div>-->

                    <!--</div>-->
                    <!--all blogs cards-->
                    <div v-if="view=='blog-all'" v-for="d,idx in allBlogs"
                         class="col-ms-12 col-sm-6 col-md-6 col-lg-5 col-xl-4 mx-auto    "
                    >
                        <a :href="'/blog/'+d.id+'/'+replaceAll(replaceAll(d.title,' ','-'),'/','')"
                           class="  d-block  mx-auto  "
                           style="height: 95%;max-width: 28rem">
                            <div class="  move-on-hover h-100 card shadow-3-primary ">
                                <div class="card-body  p-3 ">
                                    <div class="row  h-100">
                                        <div class="col-4 align-content-stretch    ">

                                            <img :src="getImage(d.docs )" class="rounded  w-100 h-100   "
                                                 style="object-fit: cover; "
                                                 alt="" @error="imgError">

                                        </div>
                                        <div class="col-8">
                                            <div class="small  mb-0 text-dark-transparent text-end  "
                                                 style="font-size: .6rem">
                                                {{ getDateTime(d.published_at) }}
                                            </div>
                                            <div class="small  mb-0 text-secondary font-weight-bold"
                                                 style="font-size: .8rem">
                                                {{
                                                    d.title && d.title.length > 30 ? d.title.substring(0, 30) + '...' : d.title
                                                }}
                                            </div>
                                            <div class="col-12 small text-dark text-opacity-75   mt-1 "
                                                 style="font-size: .7rem">
                                                {{
                                                    d.summary && d.summary.length > 70 ? d.summary.substring(0, 70) + '...' : d.summary
                                                }}

                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div v-if="view=='blog-iran'" v-for="d,idx in iranFootballBlogs"
                         class="col-ms-12 col-sm-6 col-md-6 col-lg-5 col-xl-4 mx-auto    "
                    >
                        <a :href="'/blog/'+d.id+'/'+replaceAll(replaceAll(d.title,' ','-'),'/','')"
                           class="  d-block  mx-auto  "
                           style="height: 95%;max-width: 28rem">
                            <div class="  move-on-hover h-100 card shadow-3-primary ">
                                <div class="card-body  p-3 ">
                                    <div class="row  h-100">
                                        <div class="col-4 align-content-stretch    ">

                                            <img :src="getImage(d.docs )" class="rounded  w-100 h-100   "
                                                 style="object-fit: cover; "
                                                 alt="" @error="imgError">

                                        </div>
                                        <div class="col-8">
                                            <div class="small  mb-0 text-dark-transparent text-end  "
                                                 style="font-size: .6rem">
                                                {{ getDateTime(d.published_at) }}
                                            </div>
                                            <div class="small  mb-0 text-secondary font-weight-bold"
                                                 style="font-size: .8rem">
                                                {{
                                                    d.title && d.title.length > 30 ? d.title.substring(0, 30) + '...' : d.title
                                                }}
                                            </div>
                                            <div class="col-12 small text-dark text-opacity-75   mt-1 "
                                                 style="font-size: .7rem">
                                                {{
                                                    d.summary && d.summary.length > 70 ? d.summary.substring(0, 70) + '...' : d.summary
                                                }}

                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div v-if="view=='blog-world'" v-for="d,idx in worldFootballBlogs"
                         class="col-ms-12 col-sm-6 col-md-6 col-lg-5 col-xl-4 mx-auto    "
                    >
                        <a :href="'/blog/'+d.id+'/'+replaceAll(d.title,' ','-')" class="  d-block  mx-auto  "
                           style="height: 95%;max-width: 28rem">
                            <div class="  move-on-hover h-100 card shadow-3-primary ">
                                <div class="card-body  p-3 ">
                                    <div class="row  h-100">
                                        <div class="col-4 align-content-stretch    ">

                                            <img :src="getImage(d.docs )" class="rounded  w-100 h-100   "
                                                 style="object-fit: cover; "
                                                 alt="" @error="imgError">

                                        </div>
                                        <div class="col-8">
                                            <div class="small  mb-0 text-dark-transparent text-end  "
                                                 style="font-size: .6rem">
                                                {{ getDateTime(d.published_at) }}
                                            </div>
                                            <div class="small  mb-0 text-secondary font-weight-bold"
                                                 style="font-size: .8rem">
                                                {{
                                                    d.title && d.title.length > 30 ? d.title.substring(0, 30) + '...' : d.title
                                                }}
                                            </div>
                                            <div class="col-12 small text-dark text-opacity-75   mt-1 "
                                                 style="font-size: .7rem">
                                                {{
                                                    d.summary && d.summary.length > 70 ? d.summary.substring(0, 70) + '...' : d.summary
                                                }}

                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div v-if="view=='table-all'"
                         v-for="d,idx in groupBy( allTables.filter(function(el)  {return el.type_id != 2;}),'tournament')"
                         class="col-md-6  col-xl-4">
                        <div v-if="!Array.isArray(d)" class="card shadow-primary small">

                            <div class="card-header bg-primary text-white   d-flex justify-content-between ">
                                <span class="  ">{{ d.title }}</span>
                                <a class="text-white d-block px-1 rounded hoverable-cyan"
                                   :href="'/table/'+d.id+'/'+replaceAll(d.title,' ','-')">جزییات...</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table  table-striped table-light  "
                                       style="white-space: nowrap;">
                                    <thead class="table-primary text-primary">
                                    <tr>
                                        <th class=" text-center  py-1 font-weight-bold  "

                                            v-for="h,idx in JSON.parse(d.header).filter(get3Index)">
                                            {{ h }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="row,idx in JSON.parse(d.body).map(function(el){return el.filter(get3Index);})"
                                        class=" ">
                                        <td v-for="col,idx in row" class=" py-1  text-center  overflow-hidden"
                                            style="font-size: 11px">
                                            <img v-if="col.type=='img'" :src="col.value" alt="" style="height: 3rem;">
                                            <div v-else="">{{ col.value }}</div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div v-else="" class="card shadow-primary small py-2">
                            <div class="card-header bg-primary text-white   d-flex justify-content-between ">
                                <span class="  ">{{ d[0].tournament }}</span>
                                <a class="text-white d-block px-1 rounded hoverable-cyan"
                                   :href="'/table/'+d[0].id+'/'+replaceAll(d[0].tournament,' ','-')">جزییات...</a>
                            </div>
                            <div v-for="table,idx in d" class="accordion " id="accordionPanelsStayOpenExample">
                                <div class="accordion-item mx-2">
                                    <h2 class="accordion-header  " :id="'panel-heading-'+table.id">
                                        <button class="accordion-button collapsed p-2 px-3 bg-secondary text-white"
                                                type="button"
                                                data-mdb-toggle="collapse"
                                                :data-mdb-target="'#panel-collapse-'+table.id"
                                                aria-expanded="true"
                                                :aria-controls="'panel-collapse-'+table.id">
                                            {{ table.title }}
                                        </button>
                                    </h2>
                                    <div :id="'panel-collapse-'+table.id"
                                         class="accordion-collapse collapse   "
                                         aria-labelledby="panelsStayOpen-headingOne">
                                        <div class="accordion-body p-1">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-light"
                                                       style="white-space: nowrap;">
                                                    <thead class="table-primary text-primary">
                                                    <tr>
                                                        <th scope="col" class="py-1 text-center   font-weight-bold"
                                                            v-for="h,idx in JSON.parse(table.header).filter(get3Index)">
                                                            {{ h }}
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="row,idx in JSON.parse(table.body).map(function(row){return row.filter(get3Index)})"
                                                        style="font-size: 11px">
                                                        <td v-for="col,idx in row"
                                                            class="py-1 text-center  overflow-hidden">
                                                            <img v-if="col.type=='img'" :src="col.value" alt=""
                                                                 style="height: 3rem;">
                                                            <div v-else="">{{ col.value }}</div>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div v-if="view=='conductor'" class="w-100 mx-auto">


                        <ul class="nav nav-tabs nav-fill mb-3" id="ex1" role="tablist">

                            <li v-for="d,idx in eventDays" class="nav-item" role="presentation">
                                <a
                                    class="nav-link  " :class="idx==today?'active':''"
                                    :id="'tab-'+eventIds[idx]"
                                    data-mdb-toggle="tab"
                                    :href="'#'+eventIds[idx]"
                                    role="tab"
                                    :aria-controls="eventIds[idx]"
                                    aria-selected="true"
                                >{{ idx }}</a>

                            </li>

                        </ul>
                        <!-- Tabs navs -->

                        <!-- Tabs content -->
                        <div class="tab-content" id="content">
                            <div v-for="d,idx in eventDays"
                                 class="tab-pane fade show " :class="idx==today?'active':''"
                                 :id="eventIds[idx]"
                                 role="tabpanel"
                                 :aria-labelledby="'tab-'+eventIds[idx]"
                            >
                                <div v-for="eventGroups,title in d">
                                    <div class="card">
                                        <div class="text-white text-center card-header bg-indigo p-1 ">{{ title }}</div>
                                        <div class="card-body">
                                            <div v-for="event,idx in eventGroups">


                                                <hr v-if="idx!=0" class="border border-top border-info p-0 m-0">

                                                <div class="text-primary d-flex flex-column ">
                                                    <div class="text-indigo justify-content-evenly d-flex ">
                                                        <div> {{ event.team1 }}</div>
                                                        <div v-if="event.score1!=null || event.score2!=null"
                                                             class="d-flex justify-content-center font-weight-bold text-purple ">

                                                            <div> {{ event.score1 }}</div>
                                                            <div v-if="event.score1!=null && event.score2!=null">:</div>
                                                            <div> {{ event.score2 }}</div>

                                                            <div
                                                                v-if="event.status==null && (event.score1==null && event.score2==null)"
                                                                class="small">
                                                                {{ getTime(event.time) }}
                                                            </div>

                                                        </div>
                                                        <div>{{ event.team2 }}</div>
                                                    </div>

                                                    <div v-if="event.status!=null" class="text-center smaller">
                                                        {{ event.status }}
                                                    </div>

                                                    <div class="small text-center"> {{ getTime(event.time) }}</div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="progress-line mt-1" :style="loading?'opacity:1;':'opacity:0;'"></div>
                </section>

            </div>
        </transition>
    </div>
</template>

<script>
//    import 'masonry-layout';


let scrolled = false;
let self;
let page;
export default {
    props: ['tableLink', 'blogLink', 'categoryData', 'eventsData', 'urlParams'],
    data() {
        return {
            view: 'blog-all',
            loading: false,
            allBlogs: [],
            allBlogsParams: {page: 0},
            iranFootballBlogs: [],
            iranFootballBlogsParams: {page: 0},
            worldFootballBlogs: [],
            worldFootballBlogsParams: {page: 0},
            allTables: [],
            allTablesParams: {page: 0},
            conductor: [],
            conductorParams: {page: 0},
            params: {page: 0},
            dataLink: null,
            loader: null,
            categories: this.categoryData ? JSON.parse(this.categoryData) : [],
            eventDays: this.eventsData ? JSON.parse(this.eventsData)['days'] : [],
            today: this.eventsData ? JSON.parse(this.eventsData)['today'] : [],
            eventIds: [],
        }
    },
    watch: {
        view(val) {
            this.getData(0);
        }
    },
    created() {
        if (this.urlParams) {
            this.params = JSON.parse(this.urlParams);
            if (this.params.view) {
                this.view = this.params.view;
            }
            if (!this.params.page) {
                this.params.page = 0;
            }
        }

        for (let idx in this.eventDays) {

            this.eventIds[idx] = 't' + Math.floor(Math.random() * 100000000000,);

        }

        console.log(this.eventDays);
    },
    mounted() {
        self = this;
        this.loader = document.querySelector('.progress-line');

        this.setScroll(this.loader);
        this.getData(0);
    },
    methods: {
        openLink(url) {
            window.location = url;
        },
        get3Index(el, idx, array) {
            return idx == 0 || idx == 1 || idx == array.length - 1;
        },
        replaceAll(str, search, replace) {

            if (!str) return;
            str = str.toString();

            for (let i in str) {
//                    str = str.replaceAll(eng[i], per[i]);
                if (str[i] == search)
                    str = str.replace(search, replace);
            }
            return str;

        },
        groupBy(list, key) {
            let res = [];
            let tmp = list.reduce(function (rv, x) {
                (rv[x[key]] = rv[x[key]] || []).push(x);
                return rv;
            }, {});
            for (let idx in tmp) {
                if (idx == 'null')
                    for (let jdx in tmp[idx])
                        res.push(tmp[idx][jdx]);
                else
                    res.push(tmp[idx]);
            }
            return res;
        },
        setScroll(el) {
            window.onscroll = function () {
//                    const {top, bottom, height} = this.loader.getBoundingClientRect();

                let top_of_element = el.offsetTop;
                let bottom_of_element = el.offsetTop + el.offsetHeight;
                let bottom_of_screen = window.pageYOffset + window.innerHeight;
                let top_of_screen = window.pageYOffset;


                if ((bottom_of_screen + 300 > top_of_element) && (top_of_screen < bottom_of_element + 200) && !self.loading) {
                    scrolled = true;
                    self.getData();
//                        console.log('visible')
                    // the element is visible, do something
                } else {
//                        console.log('invisible')
                    // the element is not visible, do something else
                }
            };
        },

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
        getTime(date) {
            if (date == null || date == undefined) return '';
            let d = new Date(date * 1000);
            let options = {
                hour12: false,

                hour: 'numeric',
                minute: 'numeric',
                nu: 'arab',
                calendar: 'persian',
            };
//                let day = d.toLocaleDateString('fa-IR');

            return d.toLocaleString('fa-IR', options).split(' ').reverse().join(' ');
        },

        imgError(event) {

            event.target.src = '/img/noimage.png';
            event.target.parentElement.href = '/img/noimage.png';
        },
        getImage(docs) {

            let doc;
            if (docs && docs.length > 0)
                doc = docs[0];

            if (doc)
                return '/storage/' + doc.type_id + "/" + doc.id + ".jpg";
            else
                return this.assetLink + "/noimage.png";
        },
        setParams(data) {

            if (isNaN(this.params.page))
                page = 0;


            switch (this.view) {
                case 'blog-all':
                    if (!data)
                        page = ++this.allBlogsParams.page;

                    if (this.allBlogsParams.total == 0 || (this.allBlogsParams.total > 0 && this.allBlogsParams.total <= this.allBlogs.length))
                        return false; //no data, dont try

                    this.allBlogsParams.category = null;
                    this.params = this.allBlogsParams;
                    this.dataLink = this.blogLink;
                    this.allBlogsParams.page = page;

                    if (page == 1 && !data) {
                        this.allBlogs.splice(0, this.allBlogs.length);

                    }
                    if (data) {
                        Array.prototype.push.apply(this.allBlogs, data.data);
                        this.allBlogsParams.total = data.total;

                        if (this.allBlogs.length == 0)
                            this.noData = true;
                        if (this.allBlogsParams.page > 1 && this.allBlogs.length == 0) {
                            this.noData = false;
                            this.allBlogsParams.page = 1;
                            this.getData(0);
                        }
                    }
                    break;
                case 'blog-iran':
                    if (!data)
                        page = ++this.iranFootballBlogsParams.page;
                    if (this.iranFootballBlogsParams.total == 0 || (this.iranFootballBlogsParams.total > 0 && this.iranFootballBlogsParams.total <= this.iranFootballBlogs.length))
                        return false; //no data, dont try

                    this.iranFootballBlogsParams.category = 2;
                    this.params = this.iranFootballBlogsParams;
                    this.dataLink = this.blogLink;
                    this.iranFootballBlogsParams.page = page;

                    if (page == 1 && !data) {
                        this.iranFootballBlogs.splice(0, this.iranFootballBlogs.length);

                    }
                    if (data) {
                        Array.prototype.push.apply(this.iranFootballBlogs, data.data);
                        this.iranFootballBlogsParams.total = data.total;

                        if (this.iranFootballBlogs.length == 0)
                            this.noData = true;
                        if (this.iranFootballBlogsParams.page > 1 && this.iranFootballBlogs.length == 0) {
                            this.noData = false;
                            this.iranFootballBlogsParams.page = 1;
                            this.getData(0);
                        }
                    }
                    break;
                case 'blog-world':
                    if (!data)
                        page = ++this.worldFootballBlogsParams.page;
                    if (this.worldFootballBlogsParams.total == 0 || (this.worldFootballBlogsParams.total > 0 && this.worldFootballBlogsParams.total <= this.worldFootballBlogs.length))
                        return false; //no data, dont try

                    this.worldFootballBlogsParams.category = 3;
                    this.params = this.worldFootballBlogsParams;
                    this.dataLink = this.blogLink;
                    this.worldFootballBlogsParams.page = page;

                    if (page == 1 && !data) {
                        this.worldFootballBlogs.splice(0, this.worldFootballBlogs.length);

                    }
                    if (data) {
                        Array.prototype.push.apply(this.worldFootballBlogs, data.data);
                        this.worldFootballBlogsParams.total = data.total;

                        if (this.worldFootballBlogs.length == 0)
                            this.noData = true;
                        if (this.worldFootballBlogsParams.page > 1 && this.worldFootballBlogs.length == 0) {
                            this.noData = false;
                            this.worldFootballBlogsParams.page = 1;
                            this.getData(0);
                        }
                    }
                    break;
                case 'table-all':
                    if (!data)
                        page = ++this.allTablesParams.page;
                    if (this.allTablesParams.total == 0 || (this.allTablesParams.total > 0 && this.allTablesParams.total <= this.allTables.length))
                        return false; //no data, dont try

                    this.allTablesParams.category = null;
                    this.params = {...this.allTablesParams, with_content: true};
                    this.dataLink = this.tableLink;
                    this.allTablesParams.page = page;

                    if (page == 1 && !data) {
                        this.allTables.splice(0, this.allTables.length);

                    }
                    if (data) {
                        Array.prototype.push.apply(this.allTables, data.data);
                        this.allTablesParams.total = data.total;

                        if (this.allTables.length == 0)
                            this.noData = true;
                        if (this.allTablesParams.page > 1 && this.allTables.length == 0) {
                            this.noData = false;
                            this.allTablesParams.page = 1;
                            this.getData(0);
                        }
                    }
                    break;
                case 'conductor':
                    if (!data)
                        page = ++this.conductorParams.page;
                    if (this.conductorParams.total == 0 || (this.conductorParams.total > 0 && this.conductorParams.total <= this.conductor.length))
                        return false; //no data, dont try

                    this.conductorParams.category = null;
                    this.params = {...this.conductorParams, with_content: true};
                    this.dataLink = this.tableLink;
                    this.conductorParams.page = page;

                    if (page == 1 && !data) {
                        this.conductor.splice(0, this.conductor.length);

                    }
                    if (data) {
                        Array.prototype.push.apply(this.conductor, data.data);
                        this.conductorParams.total = data.total;

                        if (this.conductor.length == 0)
                            this.noData = true;
                        if (this.conductorParams.page > 1 && this.conductor.length == 0) {
                            this.noData = false;
                            this.conductorParams.page = 1;
                            this.getData(0);
                        }
                    }
                    break;
            }


        },
        getData(page) {
            if (page) {
                this.params.page = page;
            }

            let allow = this.setParams();

            if (allow == false) return;
            history.replaceState(null, null, axios.getUri({url: '/blogs', params: {view: this.view}}));

//                this.loading.removeClass('hide');
            this.loading = true;
            this.noData = false;


            axios.get(this.dataLink, {
                params: this.params
            })
                .then((response) => {

//                            console.log(axios.getUri({url: this.url, params: response.config.params}));
//                        this.loading.addClass('hide');
                        if (response.status == 200) {


//                                console.log(response.data.data);


                            this.setParams(response.data);

//                                this.page = response.data.current_page + 1;

                            this.loading = false;

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
        }
        ,
        log(str) {
            console.log(str);
        },
    }
}
</script>
<style type="text/css">


.accordion-button::after {
    /*color: white !important;*/
    /*background-color: white;*/
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e") !important;
}
</style>
