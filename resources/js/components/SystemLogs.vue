<template>
    <div class="   shadow-lg position-relative mt-4">
        <div v-show="loading" class="  position-fixed start-0 top-50  end-0   text-center"
             style="  z-index: 10">
            <div v-for="i in  [1,1,1,1,1]"
                 class="spinner-grow mx-1   spinner-grow-sm text-danger "
                 role="status">
                <span class="visually-hidden"> </span>
            </div>

        </div>
        <div class="   ">
            <div class="card card-header bg-primary text-white">
                گزارش سیستم
            </div>


            <div class="card-body   px-0 p-sm-1  ">
                <!--chart section-->
                <div class="card ">
                    <div class="card-header bg-light ">

                        <div class="form-check form-check-inline">
                            <input checked
                                   @change="updateTypes('بازیکن')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox1"
                                   value="بازیکن"/>
                            <label class="form-check-label" for="inlineCheckbox1">بازیکن</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input checked
                                   @change="updateTypes('مربی')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox2"
                                   value="مربی"/>
                            <label class="form-check-label" for="inlineCheckbox2">مربی</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input checked
                                   @change="updateTypes('باشگاه')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox3"
                                   value="باشگاه"/>
                            <label class="form-check-label" for="inlineCheckbox3">باشگاه</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input checked
                                   @change="updateTypes('فروشگاه')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox4"
                                   value="فروشگاه"/>
                            <label class="form-check-label" for="inlineCheckbox4">فروشگاه</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input :disabled="params.type=='مالی'" checked
                                   @change="updateTypes('محصول')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox5"
                                   value="محصول"/>
                            <label class="form-check-label" for="inlineCheckbox5">محصول</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input :disabled="params.type=='مالی' || params.province!='' || params.agency!=''" checked
                                   @change="updateTypes('خبر')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox6"
                                   value="خبر"/>
                            <label class="form-check-label" for="inlineCheckbox6">خبر</label>
                        </div>
                        <div class="btn-group   my-2 ">

                            <input @change="params.type='تعداد';getData()" v-model="params.type" value="تعداد"
                                   type="radio"
                                   class="btn-check"
                                   name="gender"
                                   id="type-count"
                                   autocomplete="off"/>
                            <label class="btn btn-outline-primary px-sm-3" for="type-count">تعداد</label>
                            <input @change="params.type='مالی';getData()" v-model="params.type" value="مالی"
                                   type="radio"
                                   class="btn-check"
                                   name="gender"
                                   id="type-pay"
                                   autocomplete="off"/>
                            <label class="btn btn-outline-primary px-sm-3" for="type-pay">مالی</label>


                        </div>
                        <div class="btn-group  bg-transparent mx-1 col-md-6">

                            <input @change="params.timestamp='d';getData()" v-model="params.timestamp" value="d"
                                   type="radio"
                                   class="btn-check"
                                   name="timestamp-d"
                                   id="timestamp-d"
                                   autocomplete="off"/>
                            <label class="btn btn-outline-primary px-sm-3" for="timestamp-d">روزانه</label>
                            <input @change="params.timestamp='m';getData()" v-model="params.timestamp" value="m"
                                   type="radio"
                                   class="btn-check"
                                   name="timestamp-m"
                                   id="timestamp-m"
                                   autocomplete="off"/>
                            <label class="btn btn-outline-primary px-sm-3" for="timestamp-m">ماهانه</label>

                            <input @change="params.timestamp='y';getData()" v-model="params.timestamp" value="y"
                                   type="radio"
                                   class="btn-check"
                                   name="timestamp-y"
                                   id="timestamp-y"
                                   autocomplete="off"/>
                            <label class="btn btn-outline-primary px-sm-3" for="timestamp-y">سالانه</label>


                        </div>

                        <div class="row     mx-auto">
                            <div class="col-md-6 p-1">
                                <select class="   form-control "

                                        v-model="params.province"
                                        @change="params.county='';getData()">
                                    <option value=""> استان</option>
                                    <option class="text-dark" v-for="province in provinces"
                                            :value="province.id">
                                        {{ province.name }}

                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 p-1">
                                <select class="   form-control "

                                        v-model="params.agency"
                                        @change="getData()">
                                    <option value=""> نمایندگی</option>
                                    <option class="text-dark" v-for="agency in agencies"
                                            :value="agency.id">
                                        {{ agency.name }}

                                    </option>
                                </select>
                            </div>
                            <date-picker class="rounded-2 p-1 col-md-6 fromdate" inputClass="" :editable="true"

                                         inputFormat="YYYY/MM/DD" placeholder="از تاریخ" color="#00acc1"
                                         v-model="params.dateFrom" @change="getData()"></date-picker>
                            <date-picker class="rounded-2 p-1 col-md-6 todate" inputClass="" :editable="true"

                                         inputFormat="YYYY/MM/DD" placeholder="تا تاریخ" color="#dd77dd"
                                         v-model="params.dateTo" @change="getData()"></date-picker>


                        </div>
                    </div>
                    <canvas class="w-100 px-0 p-sm-4" id="myChart" height="400"></canvas>
                </div>
                <!--table section-->
                <div class="table-responsive position-absolute    start-0 end-0 overflow-x-scroll">
                    <table class="table  table-sm table-primary   table-striped table-light   "
                    >
                        <thead>
                        <tr v-if="params.type=='تعداد'">
                            <th class="small col-1 text-center" scope="col" style="white-space: nowrap;">شناسه</th>
                            <th class="col-5  text-center hoverable-dark" @click="sort('name',)">نام</th>
                            <th class="col-2  text-center hoverable-dark" @click="sort('type',)">نوع</th>

                            <th class=" col-3   text-center hoverable-dark" @click="sort('province_id',)">استان</th>
                            <th class=" col-3   text-center hoverable-dark" @click="sort('created_at',)">تاریخ</th>
                        </tr>
                        <tr v-else="">
                            <th class="small col-1 text-center" scope="col" style="white-space: nowrap;">#</th>
                            <th class="  text-center hoverable-dark" @click="sort('order_id',)">سفارش</th>
                            <th class=" col-3   text-center hoverable-dark" @click="sort('province_id',)">استان</th>
                            <th class="  text-center hoverable-dark" @click="sort('amount',)">مبلغ(ت)</th>
                            <th class=" text-center hoverable-dark" @click="sort('Shaparak_Ref_Id',)">شاپرک</th>
                            <th class=" text-center hoverable-dark" @click="sort('pay_for',)">نوع</th>
                            <th class="  text-center hoverable-dark" @click="sort('pay_for_id',)">شناسه نوع</th>
                            <th class=" text-center hoverable-dark" @click="sort('coupon_id',)">کوپن تخفیف</th>
                            <th class="    text-center hoverable-dark" @click="sort('created_at',)">تاریخ</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="params.type=='تعداد'" v-for="d,idx in table" class=" " :key="idx">
                            <th scope="row" class=" p-0 p-sm-1 text-center">{{d.id}}
                            </th>
                            <td class=" small p-0 p-sm-1 text-centert">
                                <a :href="getLink(d.type,d.id)">
                                    {{d.name}}
                                </a>
                            </td>
                            <td class=" small p-0 p-sm-1 text-center">{{d.type}}
                            </td>
                            <td class=" small p-0 p-sm-1 text-center">
                                {{getProvince(d.province_id)}}
                            </td>

                            <td class=" small p-0 p-sm-1 text-center">
                                {{new Date(d.created_at).toLocaleDateString('fa-IR')}}
                            </td>

                        </tr>
                        <tr v-else="" v-for="d,idj in table" class=" " :key="idj">
                            <th scope="row" class=" p-0 p-sm-1 text-center">{{d.id}}
                            </th>
                            <td class=" small p-0 p-sm-1 text-centert">
                                {{d.order_id}}
                            </td>
                            <td class=" small p-0 p-sm-1 text-center">
                                {{getProvince(d.province_id)}}
                            </td>
                            <td class=" small p-0 p-sm-1 text-center">{{d.amount}}</td>
                            <td class=" small p-0 p-sm-1 text-center">
                                {{d.Shaparak_Ref_Id}}
                            </td>
                            <td class=" small p-0 p-sm-1 text-center">
                                {{d.pay_for}}
                            </td>
                            <td class=" small p-0 p-sm-1 text-center">
                                {{d.pay_for_id}}
                            </td>
                            <td class=" small p-0 p-sm-1 text-center">
                                {{d.coupon_id ? d.coupon_id : '_'}}
                            </td>

                            <td class=" small p-0 p-sm-1 text-center">
                                {{new Date(d.created_at).toLocaleDateString('fa-IR')}}
                            </td>

                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Chart from 'chart.js/auto';
    import VuePersianDatetimePicker from 'vue3-persian-datetime-picker';
    import {shallowRef} from 'vue';

    let colors = ['rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'];
    export default {
        props: ['provinceData', 'countyData', 'agencyData', 'logLink',],
        components: {
            datePicker: VuePersianDatetimePicker
        },
        data() {
            return {
                data: [],
                params: {
                    timestamp: 'd',
                    province: "",
                    agency: "",
                    type: 'تعداد',
                    types: ['بازیکن', 'مربی', 'باشگاه', 'فروشگاه', 'محصول', 'خبر',]
                },
                link: null,
                loading: false,
                provinces: JSON.parse(this.provinceData),
                counties: JSON.parse(this.countyData),
                agencies: JSON.parse(this.agencyData),
                chart: null,
                table: [],
                toggleSort: true

            }
        },
        mounted() {
            document.querySelector('.fromdate label').append('از تاریخ');
            document.querySelector('.todate label').append('تا تاریخ');

            document.querySelectorAll('.vpd-input-group').forEach((el) => {
                el.classList.add('d-inline');
            });
            document.querySelectorAll('.vpd-input-group input').forEach((el) => {
                el.classList.add('w-100');
            });
            this.params.dateFrom = this.date('yesterday');
            this.params.dateTo = this.date();
            this.initChart();
            this.getData();
        },
        methods: {
            showDialog(type, message, click, params) {
                window.showDialog(type, message, onclick = () => click, params);
            },
            updateTypes(type) {
                var index = this.params.types.indexOf(type);
                if (index !== -1) {
                    this.params.types.splice(index, 1);
                } else {
                    this.params.types.push(type);
                }
                this.getData();
            },
            sort(by) {
                this.toggleSort = !this.toggleSort;
                this.table.sort(function (a, b) {
                    return ('' + a[by]).localeCompare('' + b[by]);
//                    return a[by] - b[by];
                });
                if (this.toggleSort)
                    this.table.reverse();
            },
            getLink(type, id) {
                let t = null;
                if (type === 'بازیکن')
                    t = 'player';
                else if (type === 'مربی')
                    t = 'coach';
                else if (type === 'باشگاه')
                    t = 'club';
                else if (type === 'فروشگاه')
                    t = 'shop';
                else if (type === 'محصول')
                    t = 'product';
                else if (type === 'خبر')
                    t = 'blog';

                return '/panel/' + t + '/edit/' + id;
            },
            initChart() {
                const ctx = document.getElementById('myChart').getContext('2d');
                this.chart = shallowRef(new Chart(ctx, {
                    type: 'line',
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'نمودار گزارشات'
                            },
                        },
                        interaction: {
                            intersect: false,
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true
                                }
                            },
                            y: {
                                beginAtZero: true,
                                display: true,
                                title: {
                                    display: true,
                                    text: 'تعداد'
                                },
//                                suggestedMin: -10,
//                                suggestedMax: 200
                            }
                        }
                    },
                    data: {},

                }));
            },
            updateChart() {
                let isPayment = this.params.types.filter((el) => el === 'پرداخت'
                ).length > 0;
                this.chart.data.datasets = [];

                let ix = -1;
                for (let i in this.data.data) {
                    ix++;
                    let counts = [];
                    if (this.params.type === 'تعداد')
                        for (let idx in this.data.dates) {
                            if (!(this.data.dates[idx] in this.data.data[i])) {
                                this.data.data[i][this.data.dates[idx]] = [];
                            }
                            counts.push(this.data.data[i][this.data.dates[idx]].length);
                        }
                    else if (this.params.type === 'مالی')
                        for (let idx in this.data.dates) {
                            if (!(this.data.dates[idx] in this.data.data[i])) {
                                this.data.data[i][this.data.dates[idx]] = [];
                            }

                            counts.push(this.data.data[i][this.data.dates[idx]].reduce((partialSum, a) => partialSum + a.amount, 0));
                        }

//                    var r = Math.floor(Math.random() * 255);
//                    var g = Math.floor(Math.random() * 255);
//                    var b = Math.floor(Math.random() * 255);
                    this.chart.data.datasets.push({
                        label: i,
//                        cubicInterpolationMode: 'monotone',
                        borderWidth: 1,
                        data: counts,
//                        borderColor: "rgb(" + r + "," + g + "," + b + ")",
                        borderColor: colors[ix],
                        backgroundColor: colors[ix],
                        tension: 0.4,
                    });
                }
                this.chart.data.labels = e2f(this.data.dates);

                this.chart.options.scales.y.title.text = this.params.type === 'مالی'
                    ? 'پرداخت (تومان)' : 'تعداد';
                this.chart.update();
            },
            getData() {
//                console.log(this.params.dateTo);
                this.loading = true;

                axios.get(this.logLink, {
                    params: this.params
                })

                    .then((response) => {
//                            console.log(response.data);
                            this.loading = false;

                            if (response.status === 200) {
                                this.data = response.data;
                                this.updateChart();
                                this.table = [];

                                this.table = [];
                                for (let i in this.data.data) {
                                    for (let j in this.data.data[i])
                                        this.table.push(this.data.data[i][j]);

                                }
                                this.table = this.table.flat();

                            }
                            //                            window.location.reload();


                        }
                    ).catch((error) => {
                    this.loading = false;

                    let errors = '';
                    if (error.response && error.response.status === 422)
                        for (let idx in error.response.data.errors)
                            errors += error.response.data.errors[idx] + '<br>';
                    else if (error.response && error.response.status === 403)
                        errors = error.response.data.message;
                    else {
                        errors = error;
                    }
                    window.showToast('danger', errors);

                });
            },
            date(day) {
                var t = new Date().getTime();
                if (day && day === 'yesterday')
                    var today = new Date(t - 24 * 60 * 60 * 1000);
                else
                    var today = new Date(t + 24 * 60 * 60 * 1000);
                let options = {
                    hour12: false,

                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',

                    calendar: 'persian',
                };
//                var dd = String(today.getDate()).padStart(2, '0');
//                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
//                var yyyy = today.getFullYear();
//                return yyyy + '/' + mm + '/' + dd;

                return f2e(today.toLocaleDateString('fa-IR', options));
            }
            ,
            log(str) {
                console.log(str);
            },
            getProvince(pId) {
                if (!pId && this.params.province)
                    pId = this.params.province;
                let p = this.provinces.find((e) => e.id == pId);
//                let c = this.counties.find((e) => e.id == cId);
                if (p)
                    return p.name;
                else
                    return ''
            },
        },
    }
</script>
