<template>
    <Swiper class="w-100"
            :modules="modules"
            :slides-per-view="'auto'"
            :space-between="10"
            :pagination="{ clickable: true }"
            :scrollbar="{ draggable: true }"
            @swiper="onSwiper"
            @slideChange="onSlideChange"
            @reachEnd="reachEnd"
    >

        <swiper-slide v-if="type=='video'" v-for="d,idx in data" :style="'width:'+width">
            <video-card :img-link="imgLink" :type="docType" :data="d" view="vertical"
                        :category-data="categoryData"></video-card>
        </swiper-slide>

        <swiper-slide v-else v-for="d,idx in data" :style="'width:'+width">
            <a :href="getLink(d.id,d.type)"
               class="m-card  d-flex align-items-start align-content-around flex-column    "
               data-toggle="modal"
               :key="d.id">
                <div class="m-card-heade bg-transparent  w-100 ">

                    <div class="d-flex justify-content-between position-relative  top-1 mx-1">

                        <!--<div class="    badge-pill bg-primary text-white   small-1"-->
                        <!--data-toggle="tooltip"-->
                        <!--data-placement="top"-->
                        <!--title="فروشنده"> {{d.name}}-->

                        <!--</div>-->

                    </div>
                    <img class="back-header-im position-absolute w-100 " style="top: -3.5rem;z-index: 2"
                         :src="assetLink+'/card-header.png'"
                         alt="">
                    <div class=" position-relative w-100">
                        <a :href="getImage(d.alldocs,d.type)" data-lity class="d-block  ">
                            <div class="  position-absolute  img-overlay">⌕</div>
                            <img class="card-im  w-100 mt-4" style="z-index: 0;height: 12rem"
                                 @error="imgError"
                                 :src="getImage(d.alldocs,d.type)" alt="">
                        </a>
                    </div>

                </div>

                <!--<img v-else src="img/school-no.png" alt=""></div>-->
                <div
                    class="m-card-body  px-2 d-flex  flex-column  align-self-stretch  justify-content-between  text-end z-index-1">

                    <div class="text-primary  my-2 text-center max-2-line "> {{ d.name }}</div>

                    <div class=" text-center     pt-1 ">
                        <span
                            class="  rounded-start   bg-primary text-white small   px-2 ">{{
                                getTypeName(d.type)
                            }} </span>

                        <span class="   rounded-end  bg-secondary text-white small   px-2 ">  {{ d.id }} </span>


                        <div class="card-divider"></div>


                        <p v-if="d.type!='pr'" class=" small text-primary text-center text-lg">
                            <i class="fas  fa-map-marker-alt"></i>
                            <span
                                class="mx-1 text-secondary">  {{
                                    getProvinceCounty(d.province_id, d.county_id)
                                }}  </span>

                        </p>
                        <p v-else="" class=" small text-primary text-center text-lg">
                            <i class="fas  fa-money-bill"></i>
                            <span class="mx-1 text-secondary">  {{ getPrice(d.province_id, d.county_id) }}  </span>

                        </p>
                        <div class="card-divider"></div>
                    </div>


                </div>
                <div class="m-card-foote  bg-transparent position-relative w-100 py-4">
                    <img class="   back-footer-im position-absolute w-100 top-0" :src="assetLink+'/card-footer.png'"
                         alt="" style=" ">
                    <!--<div class="  d-flex justify-content-center px-1  position-absolute bottom-1 z-index-3 w-100 text-center">-->

                    <!--<a class="btn bg-transparent py-2    text-white   px-1  w-100 move-on-hover hoverable"-->
                    <!--:href="'/product/'+d.name+'/'+d.id">-->

                    <!--جزییات</a>-->
                    <!--</div>-->
                </div>

            </a>
        </swiper-slide>


    </Swiper>
</template>
<script>
// import Swiper core and required modules
import {/*Navigation,*/ Pagination, Scrollbar, A11y} from 'swiper';
import VideoCard from "./VideoCard.vue";

// Import Swiper Vue.js components
import {Swiper, SwiperSlide} from 'swiper/vue';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';

// Import Swiper styles
export default {
    components: {
        VideoCard,
        Swiper,
        SwiperSlide,
    },
    props: ['dataLink', 'rootLink', 'imgLink', 'assetLink', 'width', 'provinceData', 'categoryData', 'type', 'docType'],
    data() {
        return {

            modules: [/*Navigation,*/ Pagination, Scrollbar, A11y],
            data: [],
            page: 1,
            total: -1,
            paginate: 12,
            noData: false,
            provinces: JSON.parse(this.provinceData)
        }
    },
    beforeDestroy() {

    },
    computed: {},
    mounted() {


    }
    ,
    beforeDestroy() {

    },
    created() {

//            this.getData();
    }
    ,
    updated() {


    },
    beforeUpdate() {

    }
    ,
    methods: {
        getPrice(p, d) {

            if (d && d > 0)
                return this.separator(d) + ' ت ';
            if (p && p > 0)
                return this.separator(p) + ' ت ';

            return 'نامشخص';
        },
        separator(price) {
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        getProvinceCounty(pId, cId) {
            let p = this.provinces.find((e) => e.id == pId);
            if (p) {
                let c = p.counties.find((e) => e.id == cId);
                return p.name + " _ " + c.name;
            }
            return 'نامشخص';
        },
        getLink(id, type) {

            switch (type) {
                case 'pl':
                    return '/player/' + id;
                    break;
                case 'co':
                    return '/coach/' + id;
                    break;
                case 'cl':
                    return '/club/' + id;
                    break;
                case 'sh':
                    return '/shop/' + id;
                    break;
                case 'pr':
                    return '/product/' + id;
                    break;

            }
        },
        getTypeName(type) {
            switch (type) {
                case 'pl':
                    return 'بازیکن';
                    break;
                case 'co':
                    return 'مربی';
                    break;
                case 'cl':
                    return 'باشگاه';
                    break;
                case 'sh':
                    return 'فروشگاه';
                    break;
                case 'pr':
                    return 'محصول';
                    break;

            }
        },
        getImage(docs, what) {
            //get profile for player and coach
            //get an env for club
            //get logo for shop

            let doc;

            switch (what) {
                case 'pl'  :
                    doc = docs.find(el => el.type_id == 1);
                    break;
                case  'co':
                    doc = docs.find(el => el.type_id == 1);
                    break;
                case 'cl'  :
                    doc = docs.find(el => el.type_id == 3);
                    break;
                case 'sh'  :
                    doc = docs.find(el => el.type_id == 6);
                    break;
                case 'pr'  :
                    doc = docs.find(el => el.type_id == 4);
                    break;
            }
            if (doc)
                return this.imgLink + "/" + doc.type_id + "/" + doc.id + ".jpg";
            else
                return this.assetLink + "/noimage.png";
        },
        showDialog(type, message, click) {
            window.showDialog(type, message, onclick = () => click);
        },
        onSwiper(swiper) {

        },
        onSlideChange() {

        },
        reachEnd() {

            this.getData();
        },
        getData() {

            if (this.total === 0 || (this.total > 0 && this.data.length >= this.total))
                return;
            axios.get(this.dataLink, {
                params: {
                    ...{
                        page: this.page,
                        paginate: this.paginate,
                    },


                }
            })
                .then((response) => {


//                        this.loading.addClass('hide');
                    if (response.status === 200) {
                        console.log(response.data.data.length);
                        console.log(this.page);
                        this.data = this.data.concat(response.data.data);
                        this.total = response.data.total;

//                                this.page = response.data.current_page + 1;
                        this.page++;
                        if (this.data.length === 0)
                            this.noData = true;
                        if (this.page > 1 && this.noData) {
                            this.noData = false;
                            this.page = 1;
                            this.total = -1;
                            this.getData();
                        }


                    }
                }).catch((error) => {
                console.log(error);

            });
        }
        ,
        imgError(e) {
            console.log(e);
            e.target.src = './img/noimage.png';
        },
    },

};
</script>

<style lang="scss">

</style>
